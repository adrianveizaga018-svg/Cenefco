import { Component, inject, signal, OnInit } from '@angular/core';
import { HttpErrorResponse } from '@angular/common/http';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink, ActivatedRoute } from '@angular/router';
import { NgIcon } from '@ng-icons/core';
import { RoleService } from '../../../roles/application/services/role.service';
import { agruparPermisos, GrupoPermisos, Permiso } from '../../../roles/domain/models/role.model';
import { PageTitle } from '../../../common/components/page-title/page-title';
import { extractErrorMessage } from '../../../utils/http-error';
import { AuthService } from '../../../auth/application/services/auth.service';

@Component({
  selector: 'app-rol-edit',
  imports: [FormsModule, RouterLink, NgIcon, PageTitle],
  templateUrl: './rol-edit.html',
  styles: ``
})
export class RolEdit implements OnInit {
  private service = inject(RoleService);
  private router  = inject(Router);
  private route   = inject(ActivatedRoute);
  private auth    = inject(AuthService);

  grupos: GrupoPermisos[] = [];
  permisosLoading = signal(true);

  private permisosOriginales = new Set<string>();
  private catalogoCompleto: Permiso[] = [];

  puedeAccesoTotal(): boolean {
    return this.auth.hasPermission('*');
  }

  id                    = 0;
  nombre                = '';
  descripcion           = '';
  activo                = true;
  restringidoAVendedor  = false;
  permisos              = new Set<string>();

  loading        = signal(true);
  saving         = signal(false);
  error          = signal('');

  ngOnInit(): void {
    this.id = Number(this.route.snapshot.paramMap.get('id'));
    this.service.getById(this.id).subscribe({
      next: rol => {
        this.nombre               = rol.nombre;
        this.descripcion          = rol.descripcion ?? '';
        this.activo               = rol.activo;
        this.restringidoAVendedor = rol.restringidoAVendedor ?? false;
        this.permisos             = new Set(rol.permisos ?? []);
        this.permisosOriginales = new Set(rol.permisos ?? []);
        this.loading.set(false);
        this.actualizarGrupos();
      },
      error: (err: HttpErrorResponse) => {
        this.loading.set(false);
        this.error.set('No se pudo cargar el rol.');
      },
    });

    this.service.getPermisos().subscribe({
      next: catalogo => { this.catalogoCompleto = catalogo; this.permisosLoading.set(false); this.actualizarGrupos(); },
      error: () => { this.permisosLoading.set(false); },
    });
  }

  private actualizarGrupos(): void {
    if (this.catalogoCompleto.length === 0) return;
    const visible = this.puedeAccesoTotal()
      ? this.catalogoCompleto
      : this.catalogoCompleto.filter(p => this.auth.hasPermission(p.codigo) || this.permisosOriginales.has(p.codigo));
    this.grupos = agruparPermisos(visible);
  }

  tienePermiso(key: string): boolean { return this.permisos.has(key); }

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
    const todos = keys.every(k => this.permisos.has(k));
    if (todos) keys.forEach(k => this.permisos.delete(k));
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
    this.saving.set(true);
    this.error.set('');

    this.service.update(this.id, {
      nombre:                  this.nombre.trim(),
      descripcion:             this.descripcion || null,
      activo:                  this.activo,
      permisos:                Array.from(this.permisos),
      restringido_a_vendedor:  this.restringidoAVendedor,
    }).subscribe({
      next: () => this.router.navigate(['/cenefco/roles']),
      error: (err) => {
        this.saving.set(false);
        const msg = err?.error?.errors?.nombre?.[0] ?? err?.error?.message ?? 'Error al actualizar el rol.';
        this.error.set(msg);
      },
    });
  }
}
