import { Component, computed, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { catchError, debounceTime, map, of, startWith, switchMap } from 'rxjs';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { Pagination } from '../../../common/components/pagination/pagination';
import { TipoBancoService } from '../../application/services/tipo-banco.service';
import { TipoBancoListResponse } from '../../domain/models/tipo-banco.model';
import { ToastService } from '../../../common/application/services/toast.service';
import Swal from 'sweetalert2';

type S = { type: 'loading' } | { type: 'success'; response: TipoBancoListResponse } | { type: 'error' } | { type: 'forbidden' };
const L: S = { type: 'loading' };
const E: S = { type: 'error' }; const F: S = { type: 'forbidden' };

@Component({
  selector: 'app-tipos-banco',
  imports: [PageTitle, Pagination, RouterLink, NgIcon],
  templateUrl: './tipos-banco.html',
})
export class TiposBanco {
  private service = inject(TipoBancoService);
  private toast   = inject(ToastService);

  pageIndex       = signal(1);
  pageSize        = signal(20);
  query           = signal('');
  private refresh = signal(0);

  private params = computed(() => ({
    pageIndex: this.pageIndex(),
    pageSize:  this.pageSize(),
    query:     this.query(),
    refresh:   this.refresh(),
  }));

  private state = toSignal(
    toObservable(this.params).pipe(
      debounceTime(300),
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
  get forbidden() { return this.state().type === 'forbidden'; }

  onSearch(e: Event): void      { this.query.set((e.target as HTMLInputElement).value); this.pageIndex.set(1); }
  onPageChange(p: number): void { this.pageIndex.set(p); }

  delete(id: number): void {
    Swal.fire({
      title: '¿Eliminar tipo de banco?', text: 'Esta acción no se puede deshacer.',
      icon: 'warning', showCancelButton: true,
      confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar',
    }).then(r => {
      if (r.isConfirmed) {
        this.service.delete(id).subscribe({
          next: () => {
            this.toast.success('Eliminado', 'Tipo de banco eliminado');
            this.refresh.update(n => n + 1);
          },
          error: () => this.toast.error('Error', 'No se pudo eliminar'),
        });
      }
    });
  }
}
