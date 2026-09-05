import { Component, inject, OnInit, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { RoleService } from '../../../roles/application/services/role.service';
import { agruparPermisos, GrupoPermisos, Permiso } from '../../../roles/domain/models/role.model';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { AuthService } from '../../../auth/application/services/auth.service';

@Component({
  selector: 'app-rol-create',
  imports: [FormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './rol-create.html',
  styles: ``
})
export class RolCreate implements OnInit {
  private service = inject(RoleService);
  private router  = inject(Router);
  private auth    = inject(AuthService);

  grupos: GrupoPermisos[] = [];
  permisosLoading = signal(true);

  puedeAccesoTotal(): boolean {
    return this.auth.hasPermission('*');
  }

  nombre                = '';
  descripcion           = '';
  activo                = true;
  restringidoAVendedor  = false;
  permisos              = new Set<string>();

  loading = signal(false);
  error   = signal('');

  ngOnInit(): void {
    this.service.getPermisos().subscribe({
      next: catalogo => {
        const visible = this.puedeAccesoTotal()
          ? catalogo
          : catalogo.filter((p: Permiso) => this.auth.hasPermission(p.codigo));
        this.grupos = agruparPermisos(visible);
        this.permisosLoading.set(false);
      },
      error: () => { this.permisosLoading.set(false); this.error.set('No se pudo cargar el catálogo de permisos.'); },
    });
  }

  tienePermiso(key: string): boolean {
    return this.permisos.has(key);
  }

  tieneAccesoTotal(): boolean { return this.permisos.has('*'); }

  toggleAccesoTotal(): void {
    if (!this.puedeAccesoTotal()) return;
    if (this.permisos.has('*')) this.permisos.delete('*');
    else this.permisos.add('*');
  }

  togglePermiso(key: string): void {
    if (this.permisos.has(key)) this.permisos.delete(key);
    else this.permisos.add(key);
  }

  toggleGrupo(keys: string[]): void {
    const todosSeleccionados = keys.every(k => this.permisos.has(k));
    if (todosSeleccionados) keys.forEach(k => this.permisos.delete(k));
    else keys.forEach(k => this.permisos.add(k));
  }

  grupoCompleto(keys: string[]): boolean {
    return keys.every(k => this.permisos.has(k));
  }

  grupoKeys(grupo: GrupoPermisos): string[] {
    return grupo.permisos.map(p => p.key);
  }

  guardar(): void {
    if (!this.nombre.trim()) { this.error.set('El nombre del rol es requerido.'); return; }
    this.loading.set(true);
    this.error.set('');

    this.service.create({
      nombre:                  this.nombre.trim(),
      descripcion:             this.descripcion || null,
      activo:                  this.activo,
      permisos:                Array.from(this.permisos),
      restringido_a_vendedor:  this.restringidoAVendedor,
    }).subscribe({
      next: () => this.router.navigate(['/cenefco/roles']),
      error: (err) => {
        this.loading.set(false);
        const msg = err?.error?.errors?.nombre?.[0] ?? err?.error?.message ?? 'Error al crear el rol.';
        this.error.set(msg);
      },
    });
  }
}
