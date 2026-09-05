import { ChangeDetectorRef, Component, computed, inject, OnInit, signal } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { PermisoService } from '../../application/services/permiso.service';
import { Permiso } from '../../domain/models/permiso.model';
import { ToastService } from '../../../common/application/services/toast.service';
import { MODULO_ICONS, labelDeModulo } from '../../../roles/domain/models/role.model';
import Swal from 'sweetalert2';

interface GrupoModulo {
  modulo: string;
  label: string;
  icon: string;
  items: Permiso[];
}

@Component({
  selector: 'app-permisos',
  imports: [NgIcon, PageTitle, RouterLink],
  templateUrl: './permisos.html',
})
export class Permisos implements OnInit {
  private service = inject(PermisoService);
  private toast   = inject(ToastService);
  private cdr     = inject(ChangeDetectorRef);

  permisos   = signal<Permiso[]>([]);
  loading    = signal(true);
  forbidden  = signal(false);
  error      = signal(false);
  deleting   = signal<number | null>(null);
  busqueda   = signal('');
  expandidos = signal<Set<string>>(new Set());

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    this.loading.set(true);
    this.forbidden.set(false);
    this.error.set(false);
    this.service.getAllUnpaged().subscribe({
      next: data => {
        this.permisos.set(data);
        this.loading.set(false);
        this.cdr.detectChanges();
      },
      error: (err: HttpErrorResponse) => {
        this.forbidden.set(err.status === 403);
        this.error.set(err.status !== 403);
        this.loading.set(false);
        this.cdr.detectChanges();
      },
    });
  }

  private readonly filtrados = computed(() => {
    const term = this.busqueda().trim().toLowerCase();
    if (!term) return this.permisos();
    return this.permisos().filter(p =>
      p.codigo.toLowerCase().includes(term) ||
      (p.descripcion ?? '').toLowerCase().includes(term) ||
      (p.modulo ?? '').toLowerCase().includes(term)
    );
  });

  readonly resumen = computed(() => {
    const list = this.permisos();
    return {
      total:   list.length,
      modulos: new Set(list.map(p => p.modulo ?? '__sin_modulo__')).size,
    };
  });

  readonly grupos = computed<GrupoModulo[]>(() => {
    const porModulo = new Map<string, Permiso[]>();
    for (const p of this.filtrados()) {
      const key = p.modulo ?? '__sin_modulo__';
      if (!porModulo.has(key)) porModulo.set(key, []);
      porModulo.get(key)!.push(p);
    }

    return Array.from(porModulo.entries())
      .map(([modulo, items]) => ({
        modulo,
        label: modulo === '__sin_modulo__' ? 'Sin módulo' : labelDeModulo(modulo),
        icon:  modulo === '__sin_modulo__' ? 'lucideFolder' : (MODULO_ICONS[modulo] ?? 'lucideFolder'),
        items: items.sort((a, b) => a.codigo.localeCompare(b.codigo)),
      }))
      .sort((a, b) => a.label.localeCompare(b.label));
  });

  hayBusqueda(): boolean {
    return this.busqueda().trim().length > 0;
  }

  onBuscar(event: Event): void {
    this.busqueda.set((event.target as HTMLInputElement).value);
  }

  estaExpandido(modulo: string): boolean {

    return this.hayBusqueda() || this.expandidos().has(modulo);
  }

  toggleGrupo(modulo: string): void {
    this.expandidos.update(actual => {
      const next = new Set(actual);
      if (next.has(modulo)) next.delete(modulo); else next.add(modulo);
      return next;
    });
  }

  expandirTodos(): void {
    this.expandidos.set(new Set(this.grupos().map(g => g.modulo)));
  }

  colapsarTodos(): void {
    this.expandidos.set(new Set());
  }

  delete(id: number, codigo: string): void {
    Swal.fire({
      title: `¿Eliminar el permiso "${codigo}"?`,
      text: 'Esta acción no se puede deshacer. Cualquier rol que lo tenga asignado lo perderá.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
    }).then(result => {
      if (!result.isConfirmed) return;

      this.deleting.set(id);
      this.service.delete(id).subscribe({
        next: () => {
          this.deleting.set(null);
          this.permisos.update(l => l.filter(p => p.id !== id));
          this.toast.success('¡Eliminado!', `El permiso "${codigo}" fue eliminado.`);
        },
        error: () => {
          this.toast.error('Error', 'No se pudo eliminar el permiso.');
          this.deleting.set(null);
          this.cdr.detectChanges();
        },
      });
    });
  }
}
