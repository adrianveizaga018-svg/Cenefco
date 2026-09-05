import { Component, computed, inject, signal } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { HttpErrorResponse } from '@angular/common/http';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { catchError, debounceTime, map, of, startWith, switchMap } from 'rxjs';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { Pagination } from '../../../common/components/pagination/pagination';
import { VendedorService } from '../../application/services/vendedor.service';
import { Vendedor, VendedorComisionDetalle, VendedorListResponse } from '../../domain/models/vendedor.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { AuthService } from '../../../auth/application/services/auth.service';
import { extractErrorMessage } from '../../../utils/http-error';
import Swal from 'sweetalert2';

type S = { type: 'loading' } | { type: 'success'; response: VendedorListResponse } | { type: 'error' } | { type: 'forbidden' };
const L: S = { type: 'loading' };
const E: S = { type: 'error' }; const F: S = { type: 'forbidden' };

@Component({
  selector: 'app-vendedores',
  imports: [PageTitle, Pagination, RouterLink, NgIcon, DecimalPipe],
  templateUrl: './vendedores.html',
})
export class Vendedores {
  private service = inject(VendedorService);
  private toast   = inject(ToastService);
  private auth    = inject(AuthService);

  get puedeCrear(): boolean    { return this.auth.hasPermission('ventas.crear'); }
  get puedeEditar(): boolean   { return this.auth.hasPermission('ventas.editar'); }
  get puedeEliminar(): boolean { return this.auth.hasPermission('ventas.eliminar'); }

  pageIndex       = signal(1);
  pageSize        = signal(20);
  query           = signal('');
  filtroActivo    = signal('');
  private refresh = signal(0);

  private queryDebounced = toSignal(
    toObservable(this.query).pipe(debounceTime(300)),
    { initialValue: '' }
  );

  private params = computed(() => ({
    pageIndex: this.pageIndex(),
    pageSize:  this.pageSize(),
    query:     this.queryDebounced(),
    activo:    this.filtroActivo() || undefined,
    refresh:   this.refresh(),
  }));

  private state = toSignal(
    toObservable(this.params).pipe(
      switchMap(p => this.service.getAll(p).pipe(
        map(r => ({ type: 'success', response: r } as S)),
        startWith(L),
        catchError((err: HttpErrorResponse) => of(err.status === 403 ? F : E)),
      )),
      startWith(L),
    ),
    { requireSync: true }
  );

  get items()     { const s = this.state(); return s.type === 'success' ? s.response.data : []; }
  get total()     { const s = this.state(); return s.type === 'success' ? s.response.total : 0; }
  get isLoading() { return this.state().type === 'loading'; }
  get error()     { return this.state().type === 'error'; }
  get forbidden() { return this.state().type === 'forbidden'; }

  onSearch(e: Event): void    { this.query.set((e.target as HTMLInputElement).value); this.pageIndex.set(1); }
  onFiltroActivo(e: Event): void { this.filtroActivo.set((e.target as HTMLSelectElement).value); this.pageIndex.set(1); }
  onPageChange(p: number): void  { this.pageIndex.set(p); }

  nombreCompleto(v: Vendedor): string { return `${v.nombre} ${v.apellido}`; }

  iniciales(v: Vendedor): string {
    return `${v.nombre.charAt(0)}${v.apellido.charAt(0)}`.toUpperCase();
  }

  delete(id: number): void {
    Swal.fire({
      title: '¿Eliminar vendedor?', text: 'Esta acción no se puede deshacer.',
      icon: 'warning', showCancelButton: true,
      confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar',
    }).then(r => {
      if (r.isConfirmed) {
        this.service.delete(id).subscribe({
          next: () => {
            this.toast.success('Eliminado', 'Vendedor eliminado');
            this.refresh.update(n => n + 1);
          },
          error: (err: HttpErrorResponse) => this.toast.error('Error', extractErrorMessage(err, 'No se pudo eliminar')),
        });
      }
    });
  }

  modalComisionAbierto = signal(false);
  cargandoDetalle      = signal(false);
  errorDetalle         = signal(false);
  detalleComision      = signal<VendedorComisionDetalle | null>(null);
  descargandoPdf       = signal(false);

  verComisionDetalle(item: Vendedor): void {
    this.modalComisionAbierto.set(true);
    this.cargandoDetalle.set(true);
    this.errorDetalle.set(false);
    this.detalleComision.set(null);

    this.service.getComisionDetalle(item.id).subscribe({
      next: (detalle) => {
        this.detalleComision.set(detalle);
        this.cargandoDetalle.set(false);
      },
      error: () => {
        this.errorDetalle.set(true);
        this.cargandoDetalle.set(false);
      },
    });
  }

  cerrarComisionDetalle(): void {
    this.modalComisionAbierto.set(false);
    this.detalleComision.set(null);
  }

  descargarComisionPdf(): void {
    const detalle = this.detalleComision();
    if (!detalle) return;

    this.descargandoPdf.set(true);
    this.service.getComisionDetallePdf(detalle.vendedor_id).subscribe({
      next: (blob) => {
        const url = URL.createObjectURL(blob);
        const a   = document.createElement('a');
        a.href     = url;
        a.download = `comision-${detalle.vendedor_id}.pdf`;
        a.click();
        URL.revokeObjectURL(url);
        this.descargandoPdf.set(false);
      },
      error: () => {
        this.toast.error('Error', 'No se pudo generar el PDF.');
        this.descargandoPdf.set(false);
      },
    });
  }
}
