import { Component, computed, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { catchError, map, of, startWith, switchMap } from 'rxjs';
import { Pagination } from '../../../common/components/pagination/pagination';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { AutoridadService } from '../../application/services/autoridad.service';
import { Autoridad, AutoridadListResponse } from '../../domain/models/autoridad.model';
import { ToastService } from '../../../common/application/services/toast.service';
import Swal from 'sweetalert2';
import { extractErrorMessage } from '../../../utils/http-error';

type ApiState = { type: 'loading' } | { type: 'success'; response: AutoridadListResponse } | { type: 'error' } | { type: 'forbidden' };
const LOADING: ApiState = { type: 'loading' };
const ERROR:   ApiState = { type: 'error' }; const FORBIDDEN: ApiState = { type: 'forbidden' };

@Component({
  selector: 'app-autoridades',
  imports: [NgIcon, Pagination, PageTitle, RouterLink],
  templateUrl: './autoridades.html',
  styles: ``
})
export class Autoridades {
  private autoridadService = inject(AutoridadService);
  private toast            = inject(ToastService);

  searchQuery = signal('');
  pageIndex   = signal(1);
  pageSize    = signal(10);
  togglingId  = signal<number | null>(null);
  private refreshTrigger = signal(0);

  private params = computed(() => ({
    query: this.searchQuery(), pageIndex: this.pageIndex(), pageSize: this.pageSize(), refresh: this.refreshTrigger(),
  }));

  private state = toSignal(
    toObservable(this.params).pipe(
      switchMap(p => this.autoridadService.getAll(p).pipe(
        map(response => ({ type: 'success', response } as ApiState)),
        startWith(LOADING), catchError((err: HttpErrorResponse) => of(err.status === 403 ? FORBIDDEN : ERROR)),
      )), startWith(LOADING),
    ), { requireSync: true }
  );

  get autoridades() { const s = this.state(); return s.type === 'success' ? s.response.data : []; }
  get total()       { const s = this.state(); return s.type === 'success' ? s.response.total : 0; }
  get isLoading()   { return this.state().type === 'loading'; }
  get error()       { return this.state().type === 'error'; }
  get forbidden() { return this.state().type === 'forbidden'; }

  onSearch(event: Event): void { this.searchQuery.set((event.target as HTMLInputElement).value); this.pageIndex.set(1); }
  onPageChange(page: number): void { this.pageIndex.set(page); }

  togglePublicado(autoridad: Autoridad): void {
    if (this.togglingId() !== null) return;

    const nuevoValor = !autoridad.publicado_web;
    this.togglingId.set(autoridad.id);
    this.autoridadService.update(autoridad.id, { publicado_web: nuevoValor }).subscribe({
      next: () => {
        this.togglingId.set(null);
        this.refreshTrigger.update(n => n + 1);
        this.toast.success('Listo', nuevoValor ? 'Autoridad publicada en la web.' : 'Autoridad retirada de la web.');
      },
      error: (err: HttpErrorResponse) => {
        this.togglingId.set(null);
        this.toast.error('Error', extractErrorMessage(err, 'No se pudo actualizar la publicación.'));
      }
    });
  }

  deleteAutoridad(id: number): void {
    Swal.fire({ title: '¿Eliminar autoridad?', text: 'Esta acción no se puede deshacer', icon: 'warning',
      showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar'
    }).then(result => {
      if (result.isConfirmed) {
        this.autoridadService.delete(id).subscribe({
          next: () => { this.toast.success('¡Eliminada!', 'La autoridad ha sido eliminada'); this.refreshTrigger.update(n => n + 1); },
          error: () => this.toast.error('Error', 'No se pudo eliminar la autoridad')
        });
      }
    });
  }
}
