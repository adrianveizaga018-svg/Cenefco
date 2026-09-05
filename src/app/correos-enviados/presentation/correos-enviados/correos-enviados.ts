import { Component, computed, inject, signal } from '@angular/core';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { NgClass } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { catchError, debounceTime, map, of, startWith, switchMap } from 'rxjs';
import { NgIcon } from '@ng-icons/core';
import { RouterLink } from '@angular/router';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { Pagination } from '../../../common/components/pagination/pagination';
import { CorreoEnviadoService } from '../../application/services/correo-enviado.service';
import { CorreoEnviadoListResponse } from '../../domain/models/correo-enviado.model';

type S = { type: 'loading' } | { type: 'success'; response: CorreoEnviadoListResponse } | { type: 'error' };

@Component({
  selector: 'app-correos-enviados',
  imports: [PageTitle, Pagination, NgIcon, NgClass, FormsModule, RouterLink],
  templateUrl: './correos-enviados.html',
})
export class CorreosEnviados {
  private service = inject(CorreoEnviadoService);

  pageIndex = signal(1);
  pageSize  = signal(30);
  query     = signal('');
  filtro    = signal<'todos' | 'enviado' | 'fallido'>('todos');

  private params = computed(() => ({
    pageIndex: this.pageIndex(),
    pageSize:  this.pageSize(),
    query:     this.query() || undefined,
  }));

  private state = toSignal(
    toObservable(this.params).pipe(
      debounceTime(300),
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

  private allItems() {
    const s = this.state();
    return s.type === 'success' ? s.response.data : [];
  }

  get items() {
    const items = this.allItems();
    if (this.filtro() === 'todos') return items;
    return items.filter(i => i.estado === this.filtro());
  }

  get total()     { const s = this.state(); return s.type === 'success' ? s.response.total : 0; }
  get isLoading() { return this.state().type === 'loading'; }
  get isError()   { return this.state().type === 'error'; }

  get totalFallidos() { return this.allItems().filter(i => i.estado === 'fallido').length; }
  get totalEnviados() { return this.allItems().filter(i => i.estado === 'enviado').length; }

  setFiltro(f: 'todos' | 'enviado' | 'fallido'): void {
    this.filtro.set(f);
  }

  onQueryChange(v: string): void {
    this.query.set(v);
    this.pageIndex.set(1);
  }

  onPageChange(p: number): void { this.pageIndex.set(p); }

  formatFecha(f: string | null): string {
    if (!f) return '—';
    try {
      return new Date(f).toLocaleString('es-BO', {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit',
      });
    } catch { return f; }
  }

  tipoLabel(tipo: string): string {
    const labels: Record<string, string> = {
      comprobante_inscripcion: 'Comprobante de inscripción',
    };
    return labels[tipo] ?? tipo;
  }

  referenciaLink(referenciaTipo: string | null, referenciaId: number | null): string | null {
    if (referenciaTipo === 'inscripcion' && referenciaId) {
      return `/cenefco/inscripcion-detail/${referenciaId}`;
    }
    return null;
  }
}
