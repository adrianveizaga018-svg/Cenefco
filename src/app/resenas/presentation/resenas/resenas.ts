import { Component, computed, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { SlicePipe } from '@angular/common';
import { catchError, map, of, startWith, switchMap } from 'rxjs';
import { Pagination } from '../../../common/components/pagination/pagination';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ResenaService } from '../../application/services/resena.service';
import { ResenaListResponse } from '../../domain/models/resena.model';
import { ToastService } from '../../../common/application/services/toast.service';
import Swal from 'sweetalert2';

type ApiState =
  | { type: 'loading' }
  | { type: 'success'; response: ResenaListResponse }
  | { type: 'error' } | { type: 'forbidden' };

const LOADING: ApiState = { type: 'loading' };
const ERROR:   ApiState = { type: 'error' }; const FORBIDDEN: ApiState = { type: 'forbidden' };

@Component({
  selector: 'app-resenas',
  imports: [NgIcon, Pagination, PageTitle, SlicePipe, RouterLink],
  templateUrl: './resenas.html',
  styles: ``
})
export class Resenas {
  private service = inject(ResenaService);
  private toast   = inject(ToastService);
  private router  = inject(Router);

  searchQuery      = signal('');
  filtroEstado     = signal('');
  pageIndex        = signal(1);
  pageSize         = signal(10);
  private refreshTrigger = signal(0);

  private params = computed(() => ({
    query:     this.searchQuery(),
    estado:    this.filtroEstado(),
    pageIndex: this.pageIndex(),
    pageSize:  this.pageSize(),
    refresh:   this.refreshTrigger(),
  }));

  private state = toSignal(
    toObservable(this.params).pipe(
      switchMap(p =>
        this.service.getAll(p).pipe(
          map(response => ({ type: 'success', response } as ApiState)),
          startWith(LOADING),
          catchError((err: HttpErrorResponse) => of(err.status === 403 ? FORBIDDEN : ERROR)),
        )
      ),
      startWith(LOADING),
    ),
    { requireSync: true }
  );

  get resenas()   { const s = this.state(); return s.type === 'success' ? s.response.data : []; }
  get total()     { const s = this.state(); return s.type === 'success' ? s.response.total : 0; }
  get isLoading() { return this.state().type === 'loading'; }
  get error()     { return this.state().type === 'error'; }

  get forbidden() { return this.state().type === 'forbidden'; }

  onSearch(event: Event): void {
    this.searchQuery.set((event.target as HTMLInputElement).value);
    this.pageIndex.set(1);
  }

  onFiltroEstado(event: Event): void {
    this.filtroEstado.set((event.target as HTMLSelectElement).value);
    this.pageIndex.set(1);
  }

  onPageChange(page: number): void {
    this.pageIndex.set(page);
  }

  goToDetail(id: number): void {
    this.router.navigate(['/cenefco/resena-detail', id]);
  }

  estadoBadgeClass(estado: string): string {
    const map: Record<string, string> = {
      pendiente: 'bg-warning/15 text-warning',
      aprobado:  'bg-success/15 text-success',
      rechazado: 'bg-danger/15 text-danger',
    };
    return map[estado] ?? 'bg-default-200 text-default-600';
  }

  estrellas(n: number): number[] {
    return Array.from({ length: n }, (_, i) => i);
  }

  deleteResena(id: number): void {
    Swal.fire({
      title: '¿Eliminar reseña?',
      text: 'Esta acción no se puede deshacer',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then(result => {
      if (result.isConfirmed) {
        this.service.delete(id).subscribe({
          next: () => {
            this.toast.success('¡Eliminada!', 'Reseña eliminada correctamente');
            this.refreshTrigger.update(n => n + 1);
          },
          error: () => this.toast.error('Error', 'No se pudo eliminar la reseña')
        });
      }
    });
  }
}
