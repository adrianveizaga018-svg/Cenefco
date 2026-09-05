import { Component, computed, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpErrorResponse } from '@angular/common/http';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { FormsModule } from '@angular/forms';
import { NgIcon } from '@ng-icons/core';
import { catchError, map, of, startWith, switchMap } from 'rxjs';
import { Pagination } from '../../../common/components/pagination/pagination';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { TriviaCanjeService } from '../../application/services/trivia-canje.service';
import { TriviaCanjeListResponse } from '../../domain/models/trivia-canje.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import Swal from 'sweetalert2';

type ApiState = { type: 'loading' } | { type: 'success'; response: TriviaCanjeListResponse } | { type: 'error' } | { type: 'forbidden' };
const LOADING: ApiState = { type: 'loading' };
const ERROR:   ApiState = { type: 'error' }; const FORBIDDEN: ApiState = { type: 'forbidden' };

@Component({
  selector: 'app-trivia-canjes',
  imports: [CommonModule, NgIcon, Pagination, PageTitle, FormsModule],
  templateUrl: './trivia-canjes.html',
  styles: ``
})
export class TriviaCanjes {
  private service = inject(TriviaCanjeService);
  private toast   = inject(ToastService);

  searchQuery = signal('');
  estadoFiltro = signal('');
  pageIndex   = signal(1);
  pageSize    = signal(10);
  private refreshTrigger = signal(0);

  private params = computed(() => ({
    query: this.searchQuery(), estado: this.estadoFiltro(), pageIndex: this.pageIndex(), pageSize: this.pageSize(), refresh: this.refreshTrigger(),
  }));

  private state = toSignal(
    toObservable(this.params).pipe(
      switchMap(p => this.service.getAll(p).pipe(
        map(response => ({ type: 'success', response } as ApiState)),
        startWith(LOADING), catchError((err: HttpErrorResponse) => of(err.status === 403 ? FORBIDDEN : ERROR)),
      )), startWith(LOADING),
    ), { requireSync: true }
  );

  get canjes()    { const s = this.state(); return s.type === 'success' ? s.response.data : []; }
  get total()     { const s = this.state(); return s.type === 'success' ? s.response.total : 0; }
  get isLoading() { return this.state().type === 'loading'; }
  get error()     { return this.state().type === 'error'; }
  get forbidden() { return this.state().type === 'forbidden'; }

  onSearch(event: Event): void { this.searchQuery.set((event.target as HTMLInputElement).value); this.pageIndex.set(1); }
  onEstadoChange(estado: string): void { this.estadoFiltro.set(estado); this.pageIndex.set(1); }
  onPageChange(page: number): void { this.pageIndex.set(page); }

  estadoBadgeClass(estado: string): string {
    return ({
      pendiente: 'bg-warning/10 text-warning',
      entregado: 'bg-success/10 text-success',
      cancelado: 'bg-danger/10 text-danger',
    } as Record<string, string>)[estado] ?? 'bg-default-150 text-default-500';
  }

  entregar(id: number): void {
    Swal.fire({
      title: 'Marcar como entregado',
      input: 'textarea',
      inputPlaceholder: 'Nota opcional (ej: retirado en secretaría por el estudiante)...',
      showCancelButton: true,
      confirmButtonText: 'Confirmar entrega',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#16a34a',
    }).then(result => {
      if (result.isConfirmed) {
        this.service.entregar(id, result.value || null).subscribe({
          next: () => { this.toast.success('¡Entregado!', 'El canje fue marcado como entregado'); this.refreshTrigger.update(n => n + 1); },
          error: (err: HttpErrorResponse) => this.toast.error('Error', extractErrorMessage(err, 'No se pudo marcar como entregado'))
        });
      }
    });
  }

  cancelar(id: number): void {
    Swal.fire({
      title: '¿Cancelar este canje?',
      text: 'Los puntos y el stock del premio se devolverán al usuario.',
      icon: 'warning',
      input: 'textarea',
      inputPlaceholder: 'Motivo de la cancelación (opcional)...',
      showCancelButton: true,
      confirmButtonText: 'Sí, cancelar canje',
      cancelButtonText: 'Volver',
      confirmButtonColor: '#d33',
    }).then(result => {
      if (result.isConfirmed) {
        this.service.cancelar(id, result.value || null).subscribe({
          next: () => { this.toast.success('Cancelado', 'El canje ha sido cancelado y los puntos devueltos'); this.refreshTrigger.update(n => n + 1); },
          error: (err: HttpErrorResponse) => this.toast.error('Error', extractErrorMessage(err, 'No se pudo cancelar el canje'))
        });
      }
    });
  }
}
