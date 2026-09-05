import { ChangeDetectorRef, Component, computed, inject, signal, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { RoleService } from '../../../roles/application/services/role.service';
import { Role, Permiso, agruparPermisosAsignados } from '../../../roles/domain/models/role.model';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { ToastService } from '../../../common/application/services/toast.service';
import { extractErrorMessage } from '../../../utils/http-error';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-roles',
  imports: [RouterLink, NgIcon, PageTitle],
  templateUrl: './roles.html',
  styles: ``
})
export class Roles implements OnInit {
  private service = inject(RoleService);
  private toast   = inject(ToastService);
  private cdr     = inject(ChangeDetectorRef);

  roles      = signal<Role[]>([]);
  loading    = signal(true);
  deleting   = signal<number | null>(null);
  busqueda   = signal('');
  expandidos = signal<Set<number>>(new Set());

  private catalogo: Permiso[] = [];

  readonly filtrados = computed(() => {
    const term = this.busqueda().trim().toLowerCase();
    if (!term) return this.roles();
    return this.roles().filter(r =>
      r.nombre.toLowerCase().includes(term) || (r.descripcion ?? '').toLowerCase().includes(term)
    );
  });

  readonly resumen = computed(() => {
    const list = this.roles();
    return {
      total:         list.length,
      activos:       list.filter(r => r.activo).length,
      accesoTotal:   list.filter(r => r.permisos.includes('*')).length,
      restringidos:  list.filter(r => r.restringidoAVendedor).length,
    };
  });

  ngOnInit(): void {
    this.load();
    this.service.getPermisos().subscribe({ next: catalogo => { this.catalogo = catalogo; this.cdr.detectChanges(); } });
  }

  load(): void {
    this.loading.set(true);
    this.service.getAll().subscribe({
      next: data => { this.roles.set(data); this.loading.set(false); this.cdr.detectChanges(); },
      error: (err) => {
        this.toast.error('Error', extractErrorMessage(err, 'No se pudieron cargar los roles'));
        this.loading.set(false);
        this.cdr.detectChanges();
      },
    });
  }

  onBuscar(event: Event): void {
    this.busqueda.set((event.target as HTMLInputElement).value);
  }

  toggleDetalle(id: number): void {
    this.expandidos.update(actual => {
      const next = new Set(actual);
      if (next.has(id)) next.delete(id); else next.add(id);
      return next;
    });
  }

  estaExpandido(id: number): boolean {
    return this.expandidos().has(id);
  }

  delete(id: number, nombre: string): void {
    Swal.fire({
      title: `¿Eliminar el rol "${nombre}"?`,
      text: 'Esta acción no se puede deshacer. Los usuarios que lo tengan asignado se quedarán sin ese rol.',
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
          this.roles.update(l => l.filter(r => r.id !== id));
          this.toast.success('¡Eliminado!', `El rol "${nombre}" fue eliminado.`);
        },
        error: (err) => {
          this.toast.error('Error', extractErrorMessage(err, 'No se pudo eliminar el rol'));
          this.deleting.set(null);
          this.cdr.detectChanges();
        },
      });
    });
  }

  permisosAgrupados(permisos: string[]): { modulo: string; icon: string; color: string; labels: string[] }[] {
    if (permisos.includes('*')) return [];
    return agruparPermisosAsignados(this.catalogo, permisos);
  }

  totalCatalogo(): number {
    return this.catalogo.length;
  }

  porcentaje(permisos: string[]): number {
    if (permisos.includes('*') || this.catalogo.length === 0) return 100;
    return Math.round((permisos.length / this.catalogo.length) * 100);
  }

  barraColor(pct: number): string {
    if (pct >= 70) return 'bg-danger';
    if (pct >= 35) return 'bg-warning';
    return 'bg-primary';
  }
}
