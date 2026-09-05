import { Component, OnInit, computed, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { NgIcon } from '@ng-icons/core';
import { catchError, map, of, startWith, switchMap } from 'rxjs';
import { Pagination } from '../../../common/components/pagination/pagination';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { TriviaPreguntaService } from '../../application/services/trivia-pregunta.service';
import { TriviaPreguntaListResponse } from '../../domain/models/trivia-pregunta.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { TriviaCategoriaService } from '../../../trivia-categorias/application/services/trivia-categoria.service';
import { TriviaCategoria } from '../../../trivia-categorias/domain/models/trivia-categoria.model';
import Swal from 'sweetalert2';

type ApiState = { type: 'loading' } | { type: 'success'; response: TriviaPreguntaListResponse } | { type: 'error' } | { type: 'forbidden' };
const LOADING: ApiState = { type: 'loading' };
const ERROR:   ApiState = { type: 'error' }; const FORBIDDEN: ApiState = { type: 'forbidden' };

@Component({
  selector: 'app-trivia-preguntas',
  imports: [NgIcon, Pagination, PageTitle, RouterLink, FormsModule],
  templateUrl: './trivia-preguntas.html',
  styles: ``
})
export class TriviaPreguntas implements OnInit {
  private service          = inject(TriviaPreguntaService);
  private categoriaService = inject(TriviaCategoriaService);
  private toast             = inject(ToastService);

  categorias  = signal<TriviaCategoria[]>([]);
  searchQuery = signal('');
  categoriaId = signal<number | null>(null);
  pageIndex   = signal(1);
  pageSize    = signal(10);
  private refreshTrigger = signal(0);

  private params = computed(() => ({
    query: this.searchQuery(),
    categoria_id: this.categoriaId(),
    pageIndex: this.pageIndex(),
    pageSize: this.pageSize(),
    refresh: this.refreshTrigger(),
  }));

  private state = toSignal(
    toObservable(this.params).pipe(
      switchMap(p => this.service.getAll(p).pipe(
        map(response => ({ type: 'success', response } as ApiState)),
        startWith(LOADING), catchError((err: HttpErrorResponse) => of(err.status === 403 ? FORBIDDEN : ERROR)),
      )), startWith(LOADING),
    ), { requireSync: true }
  );

  ngOnInit(): void {
    this.categoriaService.getAll({ pageSize: 200 }).subscribe({ next: (res) => this.categorias.set(res.data) });
  }

  get preguntas() { const s = this.state(); return s.type === 'success' ? s.response.data : []; }
  get total()     { const s = this.state(); return s.type === 'success' ? s.response.total : 0; }
  get isLoading() { return this.state().type === 'loading'; }
  get error()     { return this.state().type === 'error'; }
  get forbidden() { return this.state().type === 'forbidden'; }

  nombreCategoria(id: number): string {
    return this.categorias().find(c => c.id === id)?.nombre ?? `#${id}`;
  }

  onSearch(event: Event): void { this.searchQuery.set((event.target as HTMLInputElement).value); this.pageIndex.set(1); }
  onCategoriaFilterChange(id: number | string | null): void { this.categoriaId.set(id ? Number(id) : null); this.pageIndex.set(1); }
  onPageChange(page: number): void { this.pageIndex.set(page); }

  deletePregunta(id: number): void {
    Swal.fire({ title: '¿Eliminar pregunta?', text: 'Esta acción no se puede deshacer', icon: 'warning',
      showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar'
    }).then(result => {
      if (result.isConfirmed) {
        this.service.delete(id).subscribe({
          next: () => { this.toast.success('¡Eliminada!', 'La pregunta ha sido eliminada'); this.refreshTrigger.update(n => n + 1); },
          error: () => this.toast.error('Error', 'No se pudo eliminar la pregunta')
        });
      }
    });
  }
}
