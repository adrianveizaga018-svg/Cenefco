import { Component, computed, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { catchError, map, of, startWith, switchMap } from 'rxjs';
import { Pagination } from '../../../common/components/pagination/pagination';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ProgramaAcademicoService } from '../../application/services/programa-academico.service';
import { ProgramaAcademicoListResponse } from '../../domain/models/programa-academico.model';
import { ToastService } from '../../../common/application/services/toast.service';
import Swal from 'sweetalert2';

type S = { type: 'loading' } | { type: 'success'; response: ProgramaAcademicoListResponse } | { type: 'error' } | { type: 'forbidden' };
const L: S = { type: 'loading' }; const E: S = { type: 'error' }; const F: S = { type: 'forbidden' };

@Component({ selector: 'app-programas-academicos', imports: [NgIcon, Pagination, PageTitle, RouterLink], templateUrl: './programas-academicos.html' })
export class ProgramasAcademicos {
  private service = inject(ProgramaAcademicoService); private toast = inject(ToastService);
  searchQuery = signal(''); pageIndex = signal(1); pageSize = signal(10); private refresh = signal(0);
  private params = computed(() => ({ query: this.searchQuery(), pageIndex: this.pageIndex(), pageSize: this.pageSize(), refresh: this.refresh() }));
  private state = toSignal(toObservable(this.params).pipe(switchMap(p => this.service.getAll(p).pipe(map(r => ({ type: 'success', response: r } as S)), startWith(L), catchError((err: HttpErrorResponse) => of(err.status === 403 ? F : E)))), startWith(L)), { requireSync: true });
  get items()     { const s = this.state(); return s.type === 'success' ? s.response.data : []; }
  get total()     { const s = this.state(); return s.type === 'success' ? s.response.total : 0; }
  get isLoading() { return this.state().type === 'loading'; }
  get error()     { return this.state().type === 'error'; }

  get forbidden() { return this.state().type === 'forbidden'; }
  onSearch(e: Event): void { this.searchQuery.set((e.target as HTMLInputElement).value); this.pageIndex.set(1); }
  onPageChange(p: number): void { this.pageIndex.set(p); }
  delete(id: number): void {
    Swal.fire({ title: '¿Eliminar programa?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar' }).then(r => {
      if (r.isConfirmed) { this.service.delete(id).subscribe({ next: () => { this.toast.success('Eliminado', 'Programa eliminado'); this.refresh.update(n => n + 1); }, error: () => this.toast.error('Error', 'No se pudo eliminar') }); }
    });
  }
}
