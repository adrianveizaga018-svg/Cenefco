import { Component, computed, inject, signal } from '@angular/core';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { catchError, map, of, startWith, switchMap } from 'rxjs';
import { PageTitle }   from '../../../common/components/page-title/page-title';
import { Pagination }  from '../../../common/components/pagination/pagination';
import { ToastService } from '../../../common/application/services/toast.service';
import { GeneradorFormularioService } from '../../application/services/generador-formulario.service';
import { GeneradorFormularioListResponse } from '../../domain/models/generador-formulario.model';
import Swal from 'sweetalert2';

type ApiState =
  | { type: 'loading' }
  | { type: 'success'; response: GeneradorFormularioListResponse }
  | { type: 'error' };

@Component({
  selector: 'app-generador-formularios',
  standalone: true,
  imports: [NgIcon, PageTitle, Pagination, RouterLink],
  templateUrl: './generador-formularios.html',
})
export class GeneradorFormularios {
  private svc    = inject(GeneradorFormularioService);
  private toast  = inject(ToastService);
  private router = inject(Router);

  searchQuery       = signal('');
  pageIndex         = signal(1);
  readonly pageSize = 15;
  private refresh   = signal(0);

  private params = computed(() => ({
    query:     this.searchQuery(),
    pageIndex: this.pageIndex(),
    pageSize:  this.pageSize,
    refresh:   this.refresh(),
  }));

  private state = toSignal(
    toObservable(this.params).pipe(
      switchMap(p =>
        this.svc.getAll(p).pipe(
          map(response => ({ type: 'success', response } as ApiState)),
          startWith({ type: 'loading' } as ApiState),
          catchError(() => of({ type: 'error' } as ApiState)),
        )
      ),
      startWith({ type: 'loading' } as ApiState),
    ),
    { requireSync: true }
  );

  readonly items     = computed(() => { const s = this.state(); return s.type === 'success' ? s.response.data : []; });
  readonly total     = computed(() => { const s = this.state(); return s.type === 'success' ? s.response.total : 0; });
  readonly isLoading = computed(() => this.state().type === 'loading');
  readonly hasError  = computed(() => this.state().type === 'error');

  onSearch(e: Event): void {
    this.searchQuery.set((e.target as HTMLInputElement).value);
    this.pageIndex.set(1);
  }

  onPageChange(page: number): void {
    this.pageIndex.set(page);
  }

  editarCampos(id: number): void {
    this.router.navigate(['/cenefco/generador-formulario-campos', id]);
  }

  async eliminar(id: number, nombre: string): Promise<void> {
    const result = await Swal.fire({
      title:             `¿Eliminar "${nombre}"?`,
      text:              'Esta acción no se puede deshacer.',
      icon:              'warning',
      showCancelButton:  true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText:  'Cancelar',
      confirmButtonColor: '#ef4444',
    });
    if (!result.isConfirmed) return;

    this.svc.delete(id).subscribe({
      next: () => {
        this.toast.success('Eliminado', `Formulario "${nombre}" eliminado.`);
        this.refresh.update(n => n + 1);
      },
      error: () => this.toast.error('Error', 'No se pudo eliminar el formulario.'),
    });
  }
}
