import { Component, inject, signal } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PagoAcademicoService } from '../../application/services/pago-academico.service';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import { HttpErrorResponse } from '@angular/common/http';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-pago-edit',
  imports: [ReactiveFormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './pago-edit.html',
})
export class PagoEdit {
  private service = inject(PagoAcademicoService);
  private toast   = inject(ToastService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);
  private fb      = inject(FormBuilder);

  submitting = signal(false);
  loading    = signal(true);
  id         = Number(this.route.snapshot.paramMap.get('id'));

  estadoVerificacion = signal<'pendiente' | 'verificado' | 'observado'>('pendiente');
  notaVerificacion   = signal<string | null>(null);
  comprobanteActual  = signal<string | null>(null);
  comprobanteNuevo: File | null = null;

  form = this.fb.group({
    monto_pagado:        [null as number | null, [Validators.required, Validators.min(0.01)]],
    nro_boleta_bancaria: [''],
    fecha_deposito:      [''],
    observacion_pago:    [''],
  });

  private montoOriginal = 0;

  constructor() {
    this.service.getById(this.id).subscribe({
      next: (d) => {
        this.form.patchValue(d as any);
        this.montoOriginal = d.monto_pagado ?? 0;
        this.estadoVerificacion.set(d.estado_verificacion ?? 'pendiente');
        this.notaVerificacion.set(d.nota_verificacion ?? null);
        this.comprobanteActual.set(d.comprobante_archivo ?? null);
        this.loading.set(false);
      },
      error: () => {
        this.toast.error('Error', 'No se pudo cargar el pago');
        this.router.navigate(['/cenefco/pagos-academicos']);
      },
    });
  }

  onComprobanteSeleccionado(event: Event): void {
    this.comprobanteNuevo = (event.target as HTMLInputElement).files?.[0] ?? null;
  }

  onSubmit(): void {
    if (this.form.invalid) { this.form.markAllAsTouched(); return; }

    const montoNuevo = this.form.value.monto_pagado;
    const cambiaMonto = montoNuevo !== this.montoOriginal;

    const confirmar = cambiaMonto
      ? Swal.fire({
          title: '¿Modificar monto del pago?',
          html: `El monto cambiará de <b>Bs. ${this.montoOriginal ?? 0}</b> a <b>Bs. ${montoNuevo}</b>.<br>Esta acción afecta registros financieros.`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#6b7280',
          confirmButtonText: 'Sí, guardar',
          cancelButtonText: 'Cancelar',
        })
      : Promise.resolve({ isConfirmed: true });

    confirmar.then(result => {
      if (!result.isConfirmed) return;
      this.submitting.set(true);
      this.service.update(this.id, this.form.value as any, this.comprobanteNuevo).subscribe({
        next: () => {
          this.toast.success('¡Actualizado!', 'Pago actualizado correctamente');
          this.router.navigate(['/cenefco/pagos-academicos']);
        },
        error: (err: HttpErrorResponse) => {
          this.toast.error('Error', extractErrorMessage(err));
          this.submitting.set(false);
        },
      });
    });
  }
}
