import { Component, computed, inject, signal } from '@angular/core';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { RouterLink } from '@angular/router';
import { catchError, debounceTime, distinctUntilChanged, map, of, startWith, switchMap } from 'rxjs';
import { Pagination } from '../../../common/components/pagination/pagination';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { UniversidadService } from '../../application/services/universidad.service';
import { UniversidadListResponse } from '../../domain/models/universidad.model';
import { ToastService } from '../../../common/application/services/toast.service';
import Swal from 'sweetalert2';

type S = { type: 'loading' } | { type: 'success'; response: UniversidadListResponse } | { type: 'error' };
const L: S = { type: 'loading' };
const E: S = { type: 'error' };

@Component({
  selector: 'app-universidades',
  imports: [Pagination, PageTitle, RouterLink],
  templateUrl: './universidades.html',
})
export class Universidades {
  private service = inject(UniversidadService);
  private toast   = inject(ToastService);

  pageIndex       = signal(1);
  pageSize        = signal(50);
  search          = signal('');
  private refresh = signal(0);

  private params = computed(() => ({ pageIndex: this.pageIndex(), pageSize: this.pageSize(), query: this.search(), refresh: this.refresh() }));

  private state = toSignal(
    toObservable(this.params).pipe(
      debounceTime(300),
      distinctUntilChanged((a, b) => JSON.stringify(a) === JSON.stringify(b)),
      switchMap(p => this.service.getAll(p).pipe(map(r => ({ type: 'success', response: r } as S)), startWith(L), catchError(() => of(E)))),
      startWith(L),
    ),
    { requireSync: true }
  );

  get items()     { const s = this.state(); return s.type === 'success' ? s.response.data : []; }
  get total()     { const s = this.state(); return s.type === 'success' ? s.response.total : 0; }
  get isLoading() { return this.state().type === 'loading'; }

  onPageChange(p: number): void { this.pageIndex.set(p); }
  onSearch(value: string): void { this.search.set(value); this.pageIndex.set(1); }

  delete(id: number): void {
    Swal.fire({ title: '¿Eliminar universidad?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar' })
      .then(r => {
        if (r.isConfirmed) {
          this.service.delete(id).subscribe({
            next: () => { this.toast.success('Eliminado', 'Universidad eliminada'); this.refresh.update(n => n + 1); },
            error: () => this.toast.error('Error', 'No se pudo eliminar'),
          });
        }
      });
  }
}
