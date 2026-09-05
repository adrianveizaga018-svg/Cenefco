import { Component, OnInit, inject, signal, ChangeDetectorRef } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { DecimalPipe, SlicePipe } from '@angular/common';
import Swal from 'sweetalert2';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { Pagination } from '../../../common/components/pagination/pagination';
import { SearchableSelect, SelectOption } from '../../../common/components/searchable-select/searchable-select';
import { ToastService } from '../../../common/application/services/toast.service';
import { ComisionService } from '../../application/services/comision.service';
import { ComisionLiquidacion, EstadoComision } from '../../domain/models/comision.model';
import { VendedorService } from '../../../vendedores/application/services/vendedor.service';

@Component({
  selector: 'app-comisiones-historial',
  imports: [FormsModule, RouterLink, NgIcon, DecimalPipe, SlicePipe, PageTitle, Pagination, SearchableSelect],
  templateUrl: './comisiones-historial.html',
})
export class ComisionesHistorial implements OnInit {
  private service      = inject(ComisionService);
  private vendedorSvc  = inject(VendedorService);
  private toast        = inject(ToastService);
  private cdr          = inject(ChangeDetectorRef);

  items      = signal<ComisionLiquidacion[]>([]);
  total      = signal(0);
  loading    = signal(true);
  pageIndex  = signal(1);
  pageSize   = 20;

  vendedorOptions = signal<SelectOption[]>([]);
  vendedorId      = signal<number | null>(null);
  estado          = '';

  aprobandoId  = signal<number | null>(null);
  anulandoId   = signal<number | null>(null);

  modalPagoAbierto = signal(false);
  comisionAPagar    = signal<ComisionLiquidacion | null>(null);
  archivoComprobante: File | null = null;
  pagando            = signal(false);

  ngOnInit(): void {
    this.vendedorSvc.getAll({ pageSize: 200 }).subscribe({
      next: r => this.vendedorOptions.set(r.data.map(v => ({ value: v.id, label: `${v.nombre} ${v.apellido}` }))),
    });
    this.cargar();
  }

  cargar(): void {
    this.loading.set(true);
    this.service.getAll({
      pageIndex:   this.pageIndex(),
      pageSize:    this.pageSize,
      vendedor_id: this.vendedorId() ?? undefined,
      estado:      this.estado || undefined,
    }).subscribe({
      next: r => {
        this.items.set(r.data);
        this.total.set(r.total);
        this.loading.set(false);
        this.cdr.detectChanges();
      },
      error: () => {
        this.toast.error('Error', 'No se pudieron cargar las liquidaciones de comisión.');
        this.loading.set(false);
        this.cdr.detectChanges();
      },
    });
  }

  buscar(): void { this.pageIndex.set(1); this.cargar(); }
  limpiar(): void { this.vendedorId.set(null); this.estado = ''; this.buscar(); }
  onPageChange(p: number): void { this.pageIndex.set(p); this.cargar(); }

  badgeClass(estado: EstadoComision): string {
    return ({
      calculado: 'bg-amber-100 text-amber-700',
      aprobado:  'bg-info/10 text-info',
      pagado:    'bg-success/10 text-success',
      anulado:   'bg-default-100 text-default-500',
    } as Record<string, string>)[estado] ?? 'bg-default-100 text-default-500';
  }

  badgeLabel(estado: EstadoComision): string {
    return ({
      calculado: 'Calculado', aprobado: 'Aprobado', pagado: 'Pagado', anulado: 'Anulado',
    } as Record<string, string>)[estado] ?? estado;
  }

  aprobar(item: ComisionLiquidacion): void {
    Swal.fire({
      title: '¿Aprobar esta liquidación?',
      html: `Comisión de <b>Bs. ${item.monto_comision}</b> para <b>${item.vendedor_nombre}</b>.`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#22c55e',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Sí, aprobar',
      cancelButtonText: 'Cancelar',
    }).then(r => {
      if (!r.isConfirmed) return;
      this.aprobandoId.set(item.id);
      this.service.aprobar(item.id).subscribe({
        next: () => { this.toast.success('Aprobada', 'La liquidación fue aprobada.'); this.aprobandoId.set(null); this.cargar(); },
        error: (err) => {
          this.toast.error('Error', err?.error?.message ?? 'No se pudo aprobar la liquidación.');
          this.aprobandoId.set(null);
          this.cdr.detectChanges();
        },
      });
    });
  }

  anular(item: ComisionLiquidacion): void {
    Swal.fire({
      title: '¿Anular esta liquidación?',
      input: 'textarea',
      inputLabel: 'Motivo (opcional)',
      inputPlaceholder: 'Explique por qué se anula...',
      inputAttributes: { rows: '3' },
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Sí, anular',
      cancelButtonText: 'Cancelar',
    }).then(r => {
      if (!r.isConfirmed) return;
      this.anulandoId.set(item.id);
      this.service.anular(item.id, r.value || undefined).subscribe({
        next: () => { this.toast.success('Anulada', 'La liquidación fue anulada — sus pagos vuelven a estar disponibles.'); this.anulandoId.set(null); this.cargar(); },
        error: (err) => {
          this.toast.error('Error', err?.error?.message ?? 'No se pudo anular la liquidación.');
          this.anulandoId.set(null);
          this.cdr.detectChanges();
        },
      });
    });
  }

  abrirModalPago(item: ComisionLiquidacion): void {
    this.comisionAPagar.set(item);
    this.archivoComprobante = null;
    this.modalPagoAbierto.set(true);
  }

  cerrarModalPago(): void {
    this.modalPagoAbierto.set(false);
    this.comisionAPagar.set(null);
  }

  onArchivoSeleccionado(event: Event): void {
    this.archivoComprobante = (event.target as HTMLInputElement).files?.[0] ?? null;
  }

  confirmarPago(): void {
    const item = this.comisionAPagar();
    if (!item) return;
    if (!this.archivoComprobante) {
      this.toast.error('Falta el comprobante', 'Adjunta el comprobante de la transferencia o depósito.');
      return;
    }

    this.pagando.set(true);
    this.service.pagar(item.id, this.archivoComprobante).subscribe({
      next: () => {
        this.pagando.set(false);
        this.toast.success('Comisión pagada', `Se registró el pago de Bs. ${item.monto_comision} a ${item.vendedor_nombre}.`);
        this.cerrarModalPago();
        this.cargar();
      },
      error: (err) => {
        this.pagando.set(false);
        this.toast.error('Error', err?.error?.message ?? 'No se pudo registrar el pago.');
        this.cdr.detectChanges();
      },
    });
  }
}
