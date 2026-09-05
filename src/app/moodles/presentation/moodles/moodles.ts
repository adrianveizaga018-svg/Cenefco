import { Component, computed, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { catchError, debounceTime, distinctUntilChanged, map, of, startWith, switchMap } from 'rxjs';
import { Pagination } from '../../../common/components/pagination/pagination';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { MoodleService } from '../../application/services/moodle.service';
import { MoodleListResponse } from '../../domain/models/moodle.model';
import { ToastService } from '../../../common/application/services/toast.service';
import Swal from 'sweetalert2';
import { FormsModule } from '@angular/forms';

type S = { type: 'loading' } | { type: 'success'; response: MoodleListResponse } | { type: 'error' } | { type: 'forbidden' };
const L: S = { type: 'loading' };
const E: S = { type: 'error' }; const F: S = { type: 'forbidden' };

@Component({
  selector: 'app-moodles',
  imports: [NgIcon, Pagination, PageTitle, RouterLink, FormsModule],
  templateUrl: './moodles.html',
})
export class Moodles {
  private service = inject(MoodleService);
  private toast   = inject(ToastService);

  pageIndex       = signal(1);
  pageSize        = signal(20);
  searchQuery     = signal('');
  private refresh = signal(0);

  private params = computed(() => ({
    pageIndex: this.pageIndex(),
    pageSize:  this.pageSize(),
    query:     this.searchQuery(),
    refresh:   this.refresh(),
  }));

  private state = toSignal(
    toObservable(this.params).pipe(
      debounceTime(300),
      distinctUntilChanged((a, b) => JSON.stringify(a) === JSON.stringify(b)),
      switchMap(p =>
        this.service.getAll(p).pipe(
          map(r => ({ type: 'success', response: r } as S)),
          startWith(L),
          catchError((err: HttpErrorResponse) => of(err.status === 403 ? F : E)),
        )
      ),
      startWith(L),
    ),
    { requireSync: true }
  );

  get items()     { const s = this.state(); return s.type === 'success' ? s.response.data : []; }
  get total()     { const s = this.state(); return s.type === 'success' ? s.response.total : 0; }
  get isLoading() { return this.state().type === 'loading'; }
  get error()     { return this.state().type === 'error'; }

  get forbidden() { return this.state().type === 'forbidden'; }

  onSearchChange(value: string): void {
    this.searchQuery.set(value);
    this.pageIndex.set(1);
  }

  onPageChange(p: number): void { this.pageIndex.set(p); }

  delete(id: number): void {
    Swal.fire({
      title: '¿Eliminar moodle?', icon: 'warning',
      showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar',
    }).then(r => {
      if (r.isConfirmed) {
        this.service.delete(id).subscribe({
          next: () => { this.toast.success('Eliminado', 'Moodle eliminado'); this.refresh.update(n => n + 1); },
          error: () => this.toast.error('Error', 'No se pudo eliminar'),
        });
      }
    });
  }
}
