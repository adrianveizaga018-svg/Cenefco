import { Component, computed, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { RouterLink } from '@angular/router';
import { SlicePipe } from '@angular/common';
import { NgIcon } from '@ng-icons/core';
import { catchError, map, of, startWith, switchMap } from 'rxjs';
import { Pagination } from '../../../common/components/pagination/pagination';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { CartaGeneradaService } from '../../application/services/carta-generada.service';
import { CartaGeneradaListResponse } from '../../domain/models/carta-generada.model';
import { ToastService } from '../../../common/application/services/toast.service';
import Swal from 'sweetalert2';

type ApiState = { type: 'loading' } | { type: 'success'; response: CartaGeneradaListResponse } | { type: 'error' } | { type: 'forbidden' };
const LOADING: ApiState = { type: 'loading' }; const ERROR: ApiState = { type: 'error' }; const FORBIDDEN: ApiState = { type: 'forbidden' };

@Component({ selector: 'app-cartas-generadas', imports: [NgIcon, Pagination, PageTitle, RouterLink, SlicePipe], templateUrl: './cartas-generadas.html' })
export class CartasGeneradas {
  private service = inject(CartaGeneradaService); private toast = inject(ToastService);
  pageIndex = signal(1); pageSize = signal(10); private refresh = signal(0);
  private params = computed(() => ({ pageIndex: this.pageIndex(), pageSize: this.pageSize(), refresh: this.refresh() }));
  private state = toSignal(toObservable(this.params).pipe(switchMap(p => this.service.getAll(p).pipe(map(r => ({ type: 'success', response: r } as ApiState)), startWith(LOADING), catchError((err: HttpErrorResponse) => of(err.status === 403 ? FORBIDDEN : ERROR)))), startWith(LOADING)), { requireSync: true });
  get items()     { const s = this.state(); return s.type === 'success' ? s.response.data : []; }
  get total()     { const s = this.state(); return s.type === 'success' ? s.response.total : 0; }
  get isLoading() { return this.state().type === 'loading'; }
  get error()     { return this.state().type === 'error'; }

  get forbidden() { return this.state().type === 'forbidden'; }
  onPageChange(p: number): void { this.pageIndex.set(p); }
  delete(id: number): void {
    Swal.fire({ title: '¿Eliminar carta generada?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar' }).then(r => {
      if (r.isConfirmed) { this.service.delete(id).subscribe({ next: () => { this.toast.success('¡Eliminado!', 'Carta eliminada'); this.refresh.update(n => n + 1); }, error: () => this.toast.error('Error', 'No se pudo eliminar') }); }
    });
  }
}
