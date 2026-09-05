import { Routes } from '@angular/router'

import { Configuraciones } from '../../../configuraciones/presentation/configuraciones/configuraciones'
import { ConfigSitio }     from '../../../config-sitio/presentation/config-sitio/config-sitio'

import { Moodles }      from '../../../moodles/presentation/moodles/moodles'
import { MoodleCreate } from '../../../moodles/presentation/moodle-create/moodle-create'
import { MoodleEdit }   from '../../../moodles/presentation/moodle-edit/moodle-edit'

import { MdlCourses }      from '../../../mdl-courses/presentation/mdl-courses/mdl-courses'
import { MdlCourseCreate } from '../../../mdl-courses/presentation/mdl-course-create/mdl-course-create'
import { MdlCourseEdit }   from '../../../mdl-courses/presentation/mdl-course-edit/mdl-course-edit'

import { MdlUsers }      from '../../../mdl-users/presentation/mdl-users/mdl-users'
import { MdlUserCreate } from '../../../mdl-users/presentation/mdl-user-create/mdl-user-create'
import { MdlUserEdit }   from '../../../mdl-users/presentation/mdl-user-edit/mdl-user-edit'

import { UsuariosMoodle }      from '../../../usuarios-moodle/presentation/usuarios-moodle/usuarios-moodle'
import { UsuarioMoodleCreate } from '../../../usuarios-moodle/presentation/usuario-moodle-create/usuario-moodle-create'
import { UsuarioMoodleEdit }   from '../../../usuarios-moodle/presentation/usuario-moodle-edit/usuario-moodle-edit'

import { ZoomReuniones }     from '../../../zoom/presentation/zoom-reuniones/zoom-reuniones'
import { ZoomReunionCreate } from '../../../zoom/presentation/zoom-reunion-create/zoom-reunion-create'
import { ZoomGrabaciones }   from '../../../zoom/presentation/zoom-grabaciones/zoom-grabaciones'
import { ZoomCuentas }       from '../../../zoom/presentation/zoom-cuentas/zoom-cuentas'

import { CartasModelo }      from '../../../cartas-modelo/presentation/cartas-modelo/cartas-modelo'
import { CartaModeloCreate } from '../../../cartas-modelo/presentation/carta-modelo-create/carta-modelo-create'
import { CartaModeloEdit }   from '../../../cartas-modelo/presentation/carta-modelo-edit/carta-modelo-edit'

import { Cartas }      from '../../../cartas/presentation/cartas/cartas'
import { CartaCreate } from '../../../cartas/presentation/carta-create/carta-create'
import { CartaEdit }   from '../../../cartas/presentation/carta-edit/carta-edit'

import { CartasGeneradas }     from '../../../cartas-generadas/presentation/cartas-generadas/cartas-generadas'
import { CartaGeneradaCreate } from '../../../cartas-generadas/presentation/carta-generada-create/carta-generada-create'
import { CartaGeneradaDetail } from '../../../cartas-generadas/presentation/carta-generada-detail/carta-generada-detail'

export const CONFIGURACION_ROUTES: Routes = [
    { path: 'cenefco/configuraciones', component: Configuraciones, data: { title: 'Configuraciones' } },
    { path: 'cenefco/config-sitio',    component: ConfigSitio,     data: { title: 'Configuración del Sitio' } },

    { path: 'cenefco/moodles',          component: Moodles,      data: { title: 'Moodles' } },
    { path: 'cenefco/moodle-create',    component: MoodleCreate, data: { title: 'Nuevo Moodle' } },
    { path: 'cenefco/moodle-edit/:id',  component: MoodleEdit,   data: { title: 'Editar Moodle' } },

    { path: 'cenefco/mdl-courses',          component: MdlCourses,      data: { title: 'Cursos Moodle' } },
    { path: 'cenefco/mdl-course-create',    component: MdlCourseCreate, data: { title: 'Nuevo Curso Moodle' } },
    { path: 'cenefco/mdl-course-edit/:id',  component: MdlCourseEdit,   data: { title: 'Editar Curso Moodle' } },

    { path: 'cenefco/mdl-users',          component: MdlUsers,      data: { title: 'Usuarios Moodle' } },
    { path: 'cenefco/mdl-user-create',    component: MdlUserCreate, data: { title: 'Nuevo Usuario Moodle' } },
    { path: 'cenefco/mdl-user-edit/:id',  component: MdlUserEdit,   data: { title: 'Editar Usuario Moodle' } },

    { path: 'cenefco/usuarios-moodle',          component: UsuariosMoodle,      data: { title: 'Usuarios-Moodle' } },
    { path: 'cenefco/usuario-moodle-create',    component: UsuarioMoodleCreate, data: { title: 'Asignar Usuario a Moodle' } },
    { path: 'cenefco/usuario-moodle-edit/:id',  component: UsuarioMoodleEdit,   data: { title: 'Editar Usuario-Moodle' } },

    { path: 'cenefco/zoom-cuentas',         component: ZoomCuentas,       data: { title: 'Cuentas Zoom' } },
    { path: 'cenefco/zoom-reuniones',       component: ZoomReuniones,     data: { title: 'Reuniones Zoom' } },
    { path: 'cenefco/zoom-reunion-create',  component: ZoomReunionCreate, data: { title: 'Nueva Reunión Zoom' } },
    { path: 'cenefco/zoom-grabaciones',     component: ZoomGrabaciones,   data: { title: 'Grabaciones Zoom' } },

    { path: 'cenefco/cartas-modelo',          component: CartasModelo,      data: { title: 'Modelos de Cartas' } },
    { path: 'cenefco/carta-modelo-create',    component: CartaModeloCreate, data: { title: 'Nuevo Modelo de Carta' } },
    { path: 'cenefco/carta-modelo-edit/:id',  component: CartaModeloEdit,   data: { title: 'Editar Modelo de Carta' } },

    { path: 'cenefco/cartas',          component: Cartas,      data: { title: 'Cartas' } },
    { path: 'cenefco/carta-create',    component: CartaCreate, data: { title: 'Nueva Carta' } },
    { path: 'cenefco/carta-edit/:id',  component: CartaEdit,   data: { title: 'Editar Carta' } },

    { path: 'cenefco/cartas-generadas',          component: CartasGeneradas,     data: { title: 'Cartas Generadas' } },
    { path: 'cenefco/carta-generada-create',     component: CartaGeneradaCreate, data: { title: 'Generar Carta' } },
    { path: 'cenefco/carta-generada-detail/:id', component: CartaGeneradaDetail, data: { title: 'Detalle de Carta Generada' } },
]
