import { Component, inject, signal, ChangeDetectorRef, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { DecimalPipe } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { Pagination } from '../../../common/components/pagination/pagination';
import { SueldoDocenteService } from '../../application/services/sueldo-docente.service';
import { ToastService } from '../../../common/application/services/toast.service';
import { SueldoDocente, SueldoDocenteParams } from '../../domain/models/sueldo-docente.model';

@Component({
  selector: 'app-sueldos-docentes',
  imports: [NgIcon, RouterLink, DecimalPipe, FormsModule, PageTitle, Pagination],
  templateUrl: './sueldos-docentes.html',
})
export class SueldosDocentes implements OnInit {
  private service = inject(SueldoDocenteService);
  private toast   = inject(ToastService);
  private cdr     = inject(ChangeDetectorRef);

  items      = signal<SueldoDocente[]>([]);
  total      = signal(0);
  loading    = signal(true);
  pageIndex  = signal(1);
  pageSize   = 20;

  query      = '';
  periodo    = '';
  gestion    = '';
  estadoPago = '';

  ngOnInit(): void { this.cargar(); }

  cargar(): void {
    this.loading.set(true);
    const params: SueldoDocenteParams = {
      pageIndex:   this.pageIndex(),
      pageSize:    this.pageSize,
      query:       this.query || undefined,
      periodo:     this.periodo || undefined,
      gestion:     this.gestion ? +this.gestion : undefined,
      estado_pago: this.estadoPago || undefined,
    };
    this.service.getAll(params).subscribe({
      next: r => {
        this.items.set(r.data);
        this.total.set(r.total);
        this.loading.set(false);
        this.cdr.detectChanges();
      },
      error: () => {
        this.toast.error('Error', 'No se pudieron cargar los sueldos.');
        this.loading.set(false);
        this.cdr.detectChanges();
      },
    });
  }

  buscar(): void { this.pageIndex.set(1); this.cargar(); }
  onPageChange(p: number): void { this.pageIndex.set(p); this.cargar(); }

  badgeClass(estado: string): string {
    return ({
      pagado:    'bg-success/10 text-success',
      parcial:   'bg-warning/10 text-warning',
      pendiente: 'bg-danger/10 text-danger',
    } as Record<string, string>)[estado] ?? 'bg-default-100 text-default-500';
  }

  badgeLabel(estado: string): string {
    return ({ pagado: 'Pagado', parcial: 'Parcial', pendiente: 'Pendiente' } as Record<string, string>)[estado] ?? estado;
  }

  iniciales(nombre: string | null): string {
    if (!nombre) return '?';
    return nombre.split(' ').slice(0, 2).map(p => p[0]).join('').toUpperCase();
  }
}
