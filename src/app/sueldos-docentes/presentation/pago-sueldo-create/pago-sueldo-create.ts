import { Component, inject, signal, ChangeDetectorRef, OnInit } from '@angular/core';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { DecimalPipe } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { SueldoDocenteService } from '../../application/services/sueldo-docente.service';
import { ToastService } from '../../../common/application/services/toast.service';
import { SueldoDocenteDetalle } from '../../domain/models/sueldo-docente.model';

@Component({
  selector: 'app-pago-sueldo-create',
  imports: [NgIcon, RouterLink, DecimalPipe, FormsModule, PageTitle],
  templateUrl: './pago-sueldo-create.html',
})
export class PagoSueldoCreate implements OnInit {
  private service = inject(SueldoDocenteService);
  private route   = inject(ActivatedRoute);
  private router  = inject(Router);
  private toast   = inject(ToastService);
  private cdr     = inject(ChangeDetectorRef);

  sueldo   = signal<SueldoDocenteDetalle | null>(null);
  loading  = signal(true);
  saving   = signal(false);
  hasError = signal(false);

  private idSueldo!: number;

  form = {
    monto_pagado:    null as number | null,
    fecha_pago:      new Date().toISOString().slice(0, 10),
    nro_comprobante: '',
    observacion:     '',
  };

  ngOnInit(): void {
    this.idSueldo = Number(this.route.snapshot.paramMap.get('id'));
    this.service.getById(this.idSueldo).subscribe({
      next: s  => { this.sueldo.set(s);  this.loading.set(false); this.cdr.detectChanges(); },
      error: () => { this.hasError.set(true); this.loading.set(false); this.cdr.detectChanges(); },
    });
  }

  guardar(): void {
    if (!this.form.monto_pagado || !this.form.fecha_pago) {
      this.toast.error('Validación', 'Monto y fecha son obligatorios.');
      return;
    }
    if (this.form.monto_pagado <= 0) {
      this.toast.error('Validación', 'El monto debe ser mayor a 0.');
      return;
    }
    this.saving.set(true);
    this.service.addPago(this.idSueldo, this.form as any).subscribe({
      next: () => {
        this.saving.set(false);
        this.toast.success('Pago registrado', `Bs. ${this.form.monto_pagado!.toFixed(2)} registrado correctamente.`);
        this.router.navigate(['/cenefco/sueldos-docentes', this.idSueldo]);
      },
      error: () => {
        this.saving.set(false);
        this.toast.error('Error', 'No se pudo registrar el pago.');
      },
    });
  }

  cancelar(): void {
    this.router.navigate(['/cenefco/sueldos-docentes', this.idSueldo]);
  }

  badgeClass(estado: string): string {
    return ({
      pagado:    'bg-success/10 text-success',
      parcial:   'bg-warning/10 text-warning',
      pendiente: 'bg-danger/10 text-danger',
    } as Record<string, string>)[estado] ?? 'bg-default-100 text-default-500';
  }

  badgeLabel(estado: string): string {
    return ({ pagado: 'Pagado', parcial: 'Pago Parcial', pendiente: 'Pendiente' } as Record<string, string>)[estado] ?? estado;
  }

  iniciales(nombre: string | null): string {
    if (!nombre) return '?';
    return nombre.split(' ').slice(0, 2).map(p => p[0]).join('').toUpperCase();
  }
}
