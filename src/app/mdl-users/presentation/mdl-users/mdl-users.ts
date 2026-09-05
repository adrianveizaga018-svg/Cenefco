import { Component, computed, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { FormsModule } from '@angular/forms';
import { catchError, map, of, startWith, switchMap } from 'rxjs';
import { Pagination } from '../../../common/components/pagination/pagination';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { MdlUserService } from '../../application/services/mdl-user.service';
import { MdlUserListResponse } from '../../domain/models/mdl-user.model';
import { ToastService } from '../../../common/application/services/toast.service';
import Swal from 'sweetalert2';

type S = { type: 'loading' } | { type: 'success'; response: MdlUserListResponse } | { type: 'error' } | { type: 'forbidden' };
const L: S = { type: 'loading' };
const E: S = { type: 'error' }; const F: S = { type: 'forbidden' };

@Component({
  selector: 'app-mdl-users',
  imports: [NgIcon, Pagination, PageTitle, RouterLink, FormsModule],
  templateUrl: './mdl-users.html',
})
export class MdlUsers {
  private service = inject(MdlUserService);
  private toast   = inject(ToastService);

  pageIndex        = signal(1);
  pageSize         = signal(50);
  private refresh  = signal(0);
  searchQuery      = signal('');

  private params = computed(() => ({
    pageIndex: this.pageIndex(),
    pageSize:  this.pageSize(),
    query:     this.searchQuery() || undefined,
    refresh:   this.refresh(),
  }));

  private state = toSignal(
    toObservable(this.params).pipe(
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

  onPageChange(p: number): void { this.pageIndex.set(p); }

  onSearch(value: string): void {
    this.searchQuery.set(value);
    this.pageIndex.set(1);
  }

  delete(id: number): void {
    Swal.fire({
      title: '¿Eliminar usuario?', icon: 'warning',
      showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar',
    }).then(r => {
      if (r.isConfirmed) {
        this.service.delete(id).subscribe({
          next: () => { this.toast.success('Eliminado', 'Usuario eliminado'); this.refresh.update(n => n + 1); },
          error: () => this.toast.error('Error', 'No se pudo eliminar'),
        });
      }
    });
  }
}
