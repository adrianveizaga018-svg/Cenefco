import { Component, computed, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { catchError, map, of, startWith, switchMap } from 'rxjs';
import { Pagination } from '../../../common/components/pagination/pagination';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { NormaService } from '../../application/services/norma.service';
import { NormaListResponse } from '../../domain/models/norma.model';
import { ToastService } from '../../../common/application/services/toast.service';
import Swal from 'sweetalert2';
import { extractErrorMessage } from '../../../utils/http-error';

type ApiState = { type: 'loading' } | { type: 'success'; response: NormaListResponse } | { type: 'error' } | { type: 'forbidden' };
const LOADING: ApiState = { type: 'loading' };
const ERROR:   ApiState = { type: 'error' }; const FORBIDDEN: ApiState = { type: 'forbidden' };

@Component({
  selector: 'app-normas',
  imports: [NgIcon, Pagination, PageTitle, RouterLink],
  templateUrl: './normas.html',
  styles: ``
})
export class Normas {
  private normaService = inject(NormaService);
  private toast        = inject(ToastService);

  searchQuery = signal('');
  pageIndex   = signal(1);
  pageSize    = signal(10);
  private refreshTrigger = signal(0);

  private params = computed(() => ({
    query: this.searchQuery(), pageIndex: this.pageIndex(), pageSize: this.pageSize(), refresh: this.refreshTrigger(),
  }));

  private state = toSignal(
    toObservable(this.params).pipe(
      switchMap(p => this.normaService.getAll(p).pipe(
        map(response => ({ type: 'success', response } as ApiState)),
        startWith(LOADING), catchError((err: HttpErrorResponse) => of(err.status === 403 ? FORBIDDEN : ERROR)),
      )), startWith(LOADING),
    ), { requireSync: true }
  );

  get normas()    { const s = this.state(); return s.type === 'success' ? s.response.data : []; }
  get total()     { const s = this.state(); return s.type === 'success' ? s.response.total : 0; }
  get isLoading() { return this.state().type === 'loading'; }
  get error()     { return this.state().type === 'error'; }
  get forbidden() { return this.state().type === 'forbidden'; }

  onSearch(event: Event): void { this.searchQuery.set((event.target as HTMLInputElement).value); this.pageIndex.set(1); }
  onPageChange(page: number): void { this.pageIndex.set(page); }

  deleteNorma(id: number): void {
    Swal.fire({ title: '¿Eliminar norma?', text: 'Esta acción no se puede deshacer', icon: 'warning',
      showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar'
    }).then(result => {
      if (result.isConfirmed) {
        this.normaService.delete(id).subscribe({
          next: () => { this.toast.success('¡Eliminada!', 'La norma ha sido eliminada'); this.refreshTrigger.update(n => n + 1); },
          error: () => this.toast.error('Error', 'No se pudo eliminar la norma')
        });
      }
    });
  }
}
