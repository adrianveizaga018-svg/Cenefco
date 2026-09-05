import { Component, computed, inject, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { toObservable, toSignal } from '@angular/core/rxjs-interop';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { catchError, map, of, startWith, switchMap } from 'rxjs';
import { Pagination } from '../../../common/components/pagination/pagination';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { MenuItemService } from '../../application/services/menu-item.service';
import { MenuService } from '../../application/services/menu.service';
import { MenuItem, MenuItemListResponse } from '../../domain/models/menu-item.model';
import { Menu } from '../../domain/models/menu.model';
import { ToastService } from '../../../common/application/services/toast.service';
import Swal from 'sweetalert2';

type ApiState =
  | { type: 'loading' }
  | { type: 'success'; response: MenuItemListResponse }
  | { type: 'error' } | { type: 'forbidden' };

const LOADING: ApiState = { type: 'loading' };
const ERROR:   ApiState = { type: 'error' }; const FORBIDDEN: ApiState = { type: 'forbidden' };

@Component({
  selector: 'app-menu-items',
  imports: [NgIcon, Pagination, PageTitle, RouterLink],
  templateUrl: './menu-items.html',
  styles: ``
})
export class MenuItems {
  private service     = inject(MenuItemService);
  private menuService = inject(MenuService);
  private toast       = inject(ToastService);
  private route       = inject(ActivatedRoute);

  menuId = Number(this.route.snapshot.paramMap.get('menuId'));

  menu = signal<Menu | null>(null);
  allItems = signal<MenuItem[]>([]);

  searchQuery        = signal('');
  pageIndex          = signal(1);
  pageSize           = signal(20);
  private refreshTrigger = signal(0);

  private params = computed(() => ({
    menu_id:   this.menuId,
    query:     this.searchQuery(),
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

  get items()     { const s = this.state(); return s.type === 'success' ? s.response.data : []; }
  get total()     { const s = this.state(); return s.type === 'success' ? s.response.total : 0; }
  get isLoading() { return this.state().type === 'loading'; }
  get error()     { return this.state().type === 'error'; }
  get forbidden() { return this.state().type === 'forbidden'; }

  constructor() {
    this.menuService.getById(this.menuId).subscribe({
      next: m => this.menu.set(m),
    });
    this.service.getAll({ menu_id: this.menuId, pageSize: 200 }).subscribe({
      next: r => this.allItems.set(r.data),
    });
  }

  parentLabel(parentId: number | null): string {
    if (!parentId) return '—';
    const parent = this.allItems().find(i => i.id === parentId);
    return parent ? parent.etiqueta : `#${parentId}`;
  }

  onSearch(event: Event): void {
    this.searchQuery.set((event.target as HTMLInputElement).value);
    this.pageIndex.set(1);
  }

  onPageChange(page: number): void {
    this.pageIndex.set(page);
  }

  delete(id: number): void {
    Swal.fire({
      title: '¿Eliminar ítem?',
      text: 'Esta acción no se puede deshacer.',
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
            this.toast.success('¡Eliminado!', 'Ítem eliminado correctamente');
            this.refreshTrigger.update(n => n + 1);
            this.service.getAll({ menu_id: this.menuId, pageSize: 200 }).subscribe({
              next: r => this.allItems.set(r.data),
            });
          },
          error: () => this.toast.error('Error', 'No se pudo eliminar el ítem')
        });
      }
    });
  }
}
