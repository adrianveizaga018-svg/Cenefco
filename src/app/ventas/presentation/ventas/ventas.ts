import { Component, computed, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { DecimalPipe } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { catchError, map, of, startWith, switchMap } from 'rxjs';
import { Pagination } from '../../../common/components/pagination/pagination';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { VentaService } from '../../application/services/venta.service';
import { VentaListResponse } from '../../domain/models/venta.model';

type S = { type: 'loading' } | { type: 'success'; response: VentaListResponse } | { type: 'error' } | { type: 'forbidden' };
const L: S = { type: 'loading' };
const E: S = { type: 'error' }; const F: S = { type: 'forbidden' };

@Component({
  selector: 'app-ventas',
  imports: [NgIcon, RouterLink, Pagination, PageTitle, DecimalPipe, FormsModule],
  templateUrl: './ventas.html',
})
export class Ventas {
  private service = inject(VentaService);

  pageIndex  = signal(1);
  pageSize   = signal(30);
  queryInput = '';
  query      = signal('');
  estadoPagoInput = '';
  periodoInput    = '';
  gestionInput    = '';
  canalInput      = '';
  estadoPago = signal('');
  periodo    = signal('');
  gestion    = signal('');
  canal      = signal('');

  private params = computed(() => ({
    pageIndex:   this.pageIndex(),
    pageSize:    this.pageSize(),
    query:       this.query() || undefined,
    estado_pago: this.estadoPago() || undefined,
    periodo:     this.periodo() || undefined,
    gestion:     this.gestion() ? Number(this.gestion()) : undefined,
    canal_venta: this.canal() || undefined,
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
  get hasError()  { return this.state().type === 'error'; }
  get error()     { return this.state().type === 'error'; }
  get forbidden() { return this.state().type === 'forbidden'; }

  onBuscar(): void {
    this.query.set(this.queryInput);
    this.pageIndex.set(1);
  }

  onLimpiar(): void {
    this.queryInput      = '';
    this.estadoPagoInput = '';
    this.periodoInput    = '';
    this.gestionInput    = '';
    this.canalInput      = '';
    this.query.set('');
    this.estadoPago.set('');
    this.periodo.set('');
    this.gestion.set('');
    this.canal.set('');
    this.pageIndex.set(1);
  }

  onPageChange(p: number): void { this.pageIndex.set(p); }

  badgeClass(estado: string): string {
    return ({
      pagado:   'bg-success/10 text-success',
      parcial:  'bg-warning/10 text-warning',
      pendiente:'bg-danger/10 text-danger',
    } as Record<string, string>)[estado] ?? 'bg-default-100 text-default-500';
  }

  badgeLabel(estado: string): string {
    return ({ pagado: 'Pagado', parcial: 'Parcial', pendiente: 'Pendiente' } as Record<string, string>)[estado] ?? estado;
  }

  canalBadgeClass(canal: string | null): string {
    return ({
      portal:   'bg-primary/10 text-primary',
      whatsapp: 'bg-success/10 text-success',
      referido: 'bg-violet-100 text-violet-700',
      admin:    'bg-default-100 text-default-500',
    } as Record<string, string>)[canal ?? 'admin'] ?? 'bg-default-100 text-default-500';
  }

  canalLabel(canal: string | null): string {
    return ({
      portal:   'Portal',
      whatsapp: 'WhatsApp',
      referido: 'Referido',
      admin:    'Presencial',
    } as Record<string, string>)[canal ?? 'admin'] ?? (canal ?? 'Presencial');
  }
}
