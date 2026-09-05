import { Component, computed, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { DatePipe } from '@angular/common';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { catchError, map, of, startWith, switchMap } from 'rxjs';
import { Pagination } from '../../../common/components/pagination/pagination';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { UsuarioService } from '../../../usuarios/application/services/usuario.service';
import { UsuarioListResponse } from '../../../usuarios/domain/models/usuario.model';
import { ToastService } from '../../../common/application/services/toast.service';
import Swal from 'sweetalert2';
import { extractErrorMessage } from '../../../utils/http-error';

type ApiState =
  | { type: 'loading' }
  | { type: 'success'; response: UsuarioListResponse }
  | { type: 'error' } | { type: 'forbidden' };

const LOADING: ApiState = { type: 'loading' };
const ERROR:   ApiState = { type: 'error' }; const FORBIDDEN: ApiState = { type: 'forbidden' };

@Component({
  selector: 'app-usuarios',
  imports: [NgIcon, Pagination, PageTitle, RouterLink, DatePipe],
  templateUrl: './usuarios.html',
  styles: ``
})
export class Usuarios {
  private usuarioService = inject(UsuarioService);
  private toast = inject(ToastService);
  private route = inject(ActivatedRoute);

  readonly origen = (this.route.snapshot.data['origen'] as 'sistema' | 'portal') ?? 'sistema';
  readonly esPortal = this.origen === 'portal';
  readonly titulo = this.esPortal ? 'Usuarios del Portal' : 'Usuarios';
  readonly subtitulo = this.esPortal
    ? 'Cuentas registradas desde el portal (Google o registro público)'
    : 'Usuarios y RBAC';

  searchQuery = signal('');
  pageIndex   = signal(1);
  pageSize    = signal(10);
  private refreshTrigger = signal(0);

  private params = computed(() => ({
    query:     this.searchQuery(),
    pageIndex: this.pageIndex(),
    pageSize:  this.pageSize(),
    refresh:   this.refreshTrigger(),
    origen:    this.origen,
  }));

  private state = toSignal(
    toObservable(this.params).pipe(
      switchMap(p =>
        this.usuarioService.getAll(p).pipe(
          map(response => ({ type: 'success', response } as ApiState)),
          startWith(LOADING),
          catchError((err: HttpErrorResponse) => of(err.status === 403 ? FORBIDDEN : ERROR)),
        )
      ),
      startWith(LOADING),
    ),
    { requireSync: true }
  );

  get usuarios()  { const s = this.state(); return s.type === 'success' ? s.response.data : []; }
  get total()     { const s = this.state(); return s.type === 'success' ? s.response.total : 0; }
  get isLoading() { return this.state().type === 'loading'; }
  get error()     { return this.state().type === 'error'; }

  get forbidden() { return this.state().type === 'forbidden'; }

  onSearch(event: Event): void {
    this.searchQuery.set((event.target as HTMLInputElement).value);
    this.pageIndex.set(1);
  }

  onPageChange(page: number): void { this.pageIndex.set(page); }

  deleteUsuario(id: number): void {
    Swal.fire({
      title: '¿Eliminar usuario?',
      text: 'Esta acción no se puede deshacer',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then(result => {
      if (result.isConfirmed) {
        this.usuarioService.delete(id).subscribe({
          next: () => {
            this.toast.success('¡Eliminado!', 'El usuario ha sido eliminado');
            this.refreshTrigger.update(n => n + 1);
          },
          error: () => this.toast.error('Error', 'No se pudo eliminar el usuario')
        });
      }
    });
  }
}
