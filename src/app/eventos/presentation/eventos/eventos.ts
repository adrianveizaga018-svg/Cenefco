import { Component, computed, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { SlicePipe } from '@angular/common';
import { catchError, map, of, startWith, switchMap } from 'rxjs';
import { Pagination } from '../../../common/components/pagination/pagination';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { EventoService } from '../../application/services/evento.service';
import { EventoListResponse } from '../../domain/models/evento.model';
import { ToastService } from '../../../common/application/services/toast.service';
import Swal from 'sweetalert2';
import { extractErrorMessage } from '../../../utils/http-error';

type ApiState =
  | { type: 'loading' }
  | { type: 'success'; response: EventoListResponse }
  | { type: 'error' } | { type: 'forbidden' };

const LOADING: ApiState = { type: 'loading' };
const ERROR:   ApiState = { type: 'error' }; const FORBIDDEN: ApiState = { type: 'forbidden' };

@Component({
  selector: 'app-eventos',
  imports: [NgIcon, SlicePipe, Pagination, PageTitle, RouterLink],
  templateUrl: './eventos.html',
  styles: ``
})
export class Eventos {
  private eventoService = inject(EventoService);
  private toast  = inject(ToastService);

  searchQuery      = signal('');
  pageIndex        = signal(1);
  pageSize         = signal(10);
  private refreshTrigger = signal(0);

  private params = computed(() => ({
    query:     this.searchQuery(),
    pageIndex: this.pageIndex(),
    pageSize:  this.pageSize(),
    refresh:   this.refreshTrigger(),
  }));

  private state = toSignal(
    toObservable(this.params).pipe(
      switchMap(p =>
        this.eventoService.getAll(p).pipe(
          map(response => ({ type: 'success', response } as ApiState)),
          startWith(LOADING),
          catchError((err: HttpErrorResponse) => of(err.status === 403 ? FORBIDDEN : ERROR)),
        )
      ),
      startWith(LOADING),
    ),
    { requireSync: true }
  );

  get eventos() { const s = this.state(); return s.type === 'success' ? s.response.data : []; }
  get total()   { const s = this.state(); return s.type === 'success' ? s.response.total : 0; }
  get isLoading()  { return this.state().type === 'loading'; }
  get error()      { return this.state().type === 'error'; }

  get forbidden() { return this.state().type === 'forbidden'; }

  onSearch(event: Event): void {
    this.searchQuery.set((event.target as HTMLInputElement).value);
    this.pageIndex.set(1);
  }

  onPageChange(page: number): void {
    this.pageIndex.set(page);
  }

  estadoBadgeClass(estado: string): string {
    const map: Record<string, string> = {
      programado: 'bg-info/15 text-info',
      en_curso:   'bg-warning/15 text-warning',
      realizado:  'bg-success/15 text-success',
      cancelado:  'bg-danger/15 text-danger',
    };
    return map[estado] ?? 'bg-default-200 text-default-600';
  }

  deleteEvento(id: number): void {
    Swal.fire({
      title: '¿Eliminar evento?',
      text: 'Esta acción no se puede deshacer',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then(result => {
      if (result.isConfirmed) {
        this.eventoService.delete(id).subscribe({
          next: () => {
            this.toast.success('¡Eliminado!', 'El evento ha sido eliminado');
            this.refreshTrigger.update(n => n + 1);
          },
          error: () => this.toast.error('Error', 'No se pudo eliminar el evento')
        });
      }
    });
  }
}
