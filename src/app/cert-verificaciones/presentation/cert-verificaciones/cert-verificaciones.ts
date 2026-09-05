import { Component, computed, inject, signal } from '@angular/core';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { NgClass } from '@angular/common';
import { catchError, debounceTime, map, of, startWith, switchMap } from 'rxjs';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { Pagination } from '../../../common/components/pagination/pagination';
import { CertVerificacionService } from '../../application/services/cert-verificacion.service';
import { CertVerificacionListResponse } from '../../domain/models/cert-verificacion.model';

type S = { type: 'loading' } | { type: 'success'; response: CertVerificacionListResponse } | { type: 'error' };

@Component({
  selector: 'app-cert-verificaciones',
  imports: [PageTitle, Pagination, NgIcon, NgClass],
  templateUrl: './cert-verificaciones.html',
})
export class CertVerificaciones {
  private service = inject(CertVerificacionService);

  pageIndex  = signal(1);
  pageSize   = signal(30);
  filtro     = signal<'todos' | 'valido' | 'invalido'>('todos');
  expandedId = signal<number | null>(null);

  private params = computed(() => ({
    pageIndex:  this.pageIndex(),
    pageSize:   this.pageSize(),
    resultado:  this.filtro() === 'todos' ? undefined : this.filtro(),
  }));

  private state = toSignal(
    toObservable(this.params).pipe(
      debounceTime(200),
      switchMap(p =>
        this.service.getAll(p).pipe(
          map(r => ({ type: 'success', response: r } as S)),
          startWith({ type: 'loading' } as S),
          catchError(() => of({ type: 'error' } as S)),
        )
      ),
      startWith({ type: 'loading' } as S),
    ),
    { requireSync: true }
  );

  get items()     { const s = this.state(); return s.type === 'success' ? s.response.data : []; }
  get total()     { const s = this.state(); return s.type === 'success' ? s.response.total : 0; }
  get isLoading() { return this.state().type === 'loading'; }
  get isError()   { return this.state().type === 'error'; }

  get totalValidos()   { return this.items.filter(i => i.resultado === 'valido').length; }
  get totalInvalidos() { return this.items.filter(i => i.resultado !== 'valido').length; }

  setFiltro(f: 'todos' | 'valido' | 'invalido'): void {
    this.filtro.set(f);
    this.pageIndex.set(1);
  }

  toggleExpand(id: number): void {
    this.expandedId.set(this.expandedId() === id ? null : id);
  }

  formatFecha(f: string | null): string {
    if (!f) return '—';
    try {
      return new Date(f).toLocaleString('es-BO', {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit',
      });
    } catch { return f; }
  }

  browserFrom(ua: string | null): string {
    if (!ua) return '—';
    if (ua.includes('Chrome') && !ua.includes('Edg')) return 'Chrome';
    if (ua.includes('Firefox')) return 'Firefox';
    if (ua.includes('Safari') && !ua.includes('Chrome')) return 'Safari';
    if (ua.includes('Edg')) return 'Edge';
    if (ua.includes('curl')) return 'cURL';
    return ua.slice(0, 30) + '…';
  }

  onPageChange(p: number): void { this.pageIndex.set(p); }
}
