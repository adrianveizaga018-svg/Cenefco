import { Routes } from '@angular/router'
import { permissionGuard } from '../../../auth/guards/permission.guard'

import { Usuarios }      from '../../../usuarios/presentation/usuarios/usuarios'
import { UsuarioCreate } from '../../../usuarios/presentation/usuario-create/usuario-create'
import { UsuarioEdit }   from '../../../usuarios/presentation/usuario-edit/usuario-edit'

import { Roles }     from '../../../roles/presentation/roles/roles'
import { RolCreate } from '../../../roles/presentation/rol-create/rol-create'
import { RolEdit }   from '../../../roles/presentation/rol-edit/rol-edit'

import { Permisos }      from '../../../permisos/presentation/permisos/permisos'
import { PermisoCreate } from '../../../permisos/presentation/permiso-create/permiso-create'
import { PermisoEdit }   from '../../../permisos/presentation/permiso-edit/permiso-edit'

import { NotificacionesSistema } from '../../../notificaciones-sistema/presentation/notificaciones-sistema/notificaciones-sistema'

import { Vendedores }      from '../../../vendedores/presentation/vendedores/vendedores'
import { VendedorCreate }  from '../../../vendedores/presentation/vendedor-create/vendedor-create'
import { VendedorEdit }    from '../../../vendedores/presentation/vendedor-edit/vendedor-edit'

import { MiPerfil } from '../../../cuenta/presentation/mi-perfil/mi-perfil'

import { NotificacionesPage }    from '../../../notificaciones/presentation/notificaciones/notificaciones'
import { NotificacionEnviar }    from '../../../notificaciones/presentation/notificacion-enviar/notificacion-enviar'
import { NotificacionesEnviadas } from '../../../notificaciones/presentation/notificaciones-enviadas/notificaciones-enviadas'

export const USUARIOS_ROUTES: Routes = [
    { path: 'cenefco/usuarios',          component: Usuarios,      data: { title: 'Usuarios', origen: 'sistema' },      canActivate: [permissionGuard('usuarios.ver')] },
    { path: 'cenefco/usuarios-portal',   component: Usuarios,      data: { title: 'Usuarios del Portal', origen: 'portal' }, canActivate: [permissionGuard('usuarios.ver')] },
    { path: 'cenefco/usuario-create',    component: UsuarioCreate, data: { title: 'Nuevo Usuario' }, canActivate: [permissionGuard('usuarios.crear')] },
    { path: 'cenefco/usuario-edit/:id',  component: UsuarioEdit,   data: { title: 'Editar Usuario' }, canActivate: [permissionGuard('usuarios.editar')] },

    { path: 'cenefco/roles',         component: Roles,     data: { title: 'Roles' },        canActivate: [permissionGuard('usuarios.ver')] },
    { path: 'cenefco/rol-create',    component: RolCreate, data: { title: 'Nuevo Rol' },     canActivate: [permissionGuard('usuarios.crear')] },
    { path: 'cenefco/rol-edit/:id',  component: RolEdit,   data: { title: 'Editar Rol' },    canActivate: [permissionGuard('usuarios.editar')] },

    { path: 'cenefco/permisos',          component: Permisos,      data: { title: 'Permisos' },      canActivate: [permissionGuard('usuarios.ver')] },
    { path: 'cenefco/permiso-create',    component: PermisoCreate, data: { title: 'Nuevo Permiso' }, canActivate: [permissionGuard('usuarios.crear')] },
    { path: 'cenefco/permiso-edit/:id',  component: PermisoEdit,   data: { title: 'Editar Permiso' }, canActivate: [permissionGuard('usuarios.editar')] },

    { path: 'cenefco/notificaciones-sistema', component: NotificacionesSistema, data: { title: 'Notificaciones del Sistema' } },

    { path: 'cenefco/vendedores',          component: Vendedores,     data: { title: 'Vendedores' },      canActivate: [permissionGuard('ventas.ver')] },
    { path: 'cenefco/vendedor-create',     component: VendedorCreate, data: { title: 'Nuevo Vendedor' }, canActivate: [permissionGuard('ventas.crear')] },
    { path: 'cenefco/vendedor-edit/:id',   component: VendedorEdit,   data: { title: 'Editar Vendedor' }, canActivate: [permissionGuard('ventas.editar')] },

    { path: 'cenefco/mi-perfil', component: MiPerfil, data: { title: 'Mi Perfil' } },

    { path: 'cenefco/notificaciones',         component: NotificacionesPage,    data: { title: 'Notificaciones' } },
    { path: 'cenefco/notificaciones/enviar',  component: NotificacionEnviar,    data: { title: 'Enviar Comunicado' } },
    { path: 'cenefco/notificaciones/enviados', component: NotificacionesEnviadas, data: { title: 'Comunicados Enviados' } },
]
