import { Routes } from '@angular/router'

import { Ciudades }     from '../../../ciudades/presentation/ciudades/ciudades'
import { CiudadCreate } from '../../../ciudades/presentation/ciudad-create/ciudad-create'
import { CiudadEdit }   from '../../../ciudades/presentation/ciudad-edit/ciudad-edit'

import { Profesiones }     from '../../../profesiones/presentation/profesiones/profesiones'
import { ProfesionCreate } from '../../../profesiones/presentation/profesion-create/profesion-create'
import { ProfesionEdit }   from '../../../profesiones/presentation/profesion-edit/profesion-edit'

import { Niveles }     from '../../../niveles/presentation/niveles/niveles'
import { NivelCreate } from '../../../niveles/presentation/nivel-create/nivel-create'
import { NivelEdit }   from '../../../niveles/presentation/nivel-edit/nivel-edit'

import { TiposPago }      from '../../../tipos-pago/presentation/tipos-pago/tipos-pago'
import { TipoPagoCreate } from '../../../tipos-pago/presentation/tipo-pago-create/tipo-pago-create'
import { TipoPagoEdit }   from '../../../tipos-pago/presentation/tipo-pago-edit/tipo-pago-edit'

import { TiposBanco }      from '../../../tipos-banco/presentation/tipos-banco/tipos-banco'
import { TipoBancoCreate } from '../../../tipos-banco/presentation/tipo-banco-create/tipo-banco-create'
import { TipoBancoEdit }   from '../../../tipos-banco/presentation/tipo-banco-edit/tipo-banco-edit'

import { TiposUniversidad }      from '../../../tipos-universidad/presentation/tipos-universidad/tipos-universidad'
import { TipoUniversidadCreate } from '../../../tipos-universidad/presentation/tipo-universidad-create/tipo-universidad-create'
import { TipoUniversidadEdit }   from '../../../tipos-universidad/presentation/tipo-universidad-edit/tipo-universidad-edit'

import { TiposPostgrado }      from '../../../tipos-postgrado/presentation/tipos-postgrado/tipos-postgrado'
import { TipoPostgradoCreate } from '../../../tipos-postgrado/presentation/tipo-postgrado-create/tipo-postgrado-create'
import { TipoPostgradoEdit }   from '../../../tipos-postgrado/presentation/tipo-postgrado-edit/tipo-postgrado-edit'

import { ConfiguracionAcademica }       from '../../../configuracion-academica/presentation/configuracion-academica/configuracion-academica'
import { ConfiguracionAcademicaCreate } from '../../../configuracion-academica/presentation/configuracion-academica-create/configuracion-academica-create'
import { ConfiguracionAcademicaEdit }   from '../../../configuracion-academica/presentation/configuracion-academica-edit/configuracion-academica-edit'

import { Universidades }     from '../../../universidades/presentation/universidades/universidades'
import { UniversidadCreate } from '../../../universidades/presentation/universidad-create/universidad-create'
import { UniversidadEdit }   from '../../../universidades/presentation/universidad-edit/universidad-edit'

import { GradosAcademicos } from '../../../grado-academico/presentation/grados-academicos/grados-academicos'
import { GradoCreate }      from '../../../grado-academico/presentation/grado-create/grado-create'
import { GradoEdit }        from '../../../grado-academico/presentation/grado-edit/grado-edit'

import { Expedido }      from '../../../expedido/presentation/expedido/expedido'
import { ExpedidoCreate } from '../../../expedido/presentation/expedido-create/expedido-create'
import { ExpedidoEdit }   from '../../../expedido/presentation/expedido-edit/expedido-edit'

export const CATALOGOS_ROUTES: Routes = [
    { path: 'cenefco/ciudades',         component: Ciudades,      data: { title: 'Ciudades' } },
    { path: 'cenefco/ciudad-create',    component: CiudadCreate,  data: { title: 'Nueva Ciudad' } },
    { path: 'cenefco/ciudad-edit/:id',  component: CiudadEdit,    data: { title: 'Editar Ciudad' } },

    { path: 'cenefco/profesiones',          component: Profesiones,     data: { title: 'Profesiones' } },
    { path: 'cenefco/profesion-create',     component: ProfesionCreate, data: { title: 'Nueva Profesión' } },
    { path: 'cenefco/profesion-edit/:id',   component: ProfesionEdit,   data: { title: 'Editar Profesión' } },

    { path: 'cenefco/niveles',         component: Niveles,     data: { title: 'Niveles' } },
    { path: 'cenefco/nivel-create',    component: NivelCreate, data: { title: 'Nuevo Nivel' } },
    { path: 'cenefco/nivel-edit/:id',  component: NivelEdit,   data: { title: 'Editar Nivel' } },

    { path: 'cenefco/tipos-pago',           component: TiposPago,      data: { title: 'Tipos de Pago' } },
    { path: 'cenefco/tipo-pago-create',     component: TipoPagoCreate, data: { title: 'Nuevo Tipo de Pago' } },
    { path: 'cenefco/tipo-pago-edit/:id',   component: TipoPagoEdit,   data: { title: 'Editar Tipo de Pago' } },

    { path: 'cenefco/tipos-banco',          component: TiposBanco,      data: { title: 'Tipos de Banco' } },
    { path: 'cenefco/tipo-banco-create',    component: TipoBancoCreate, data: { title: 'Nuevo Tipo de Banco' } },
    { path: 'cenefco/tipo-banco-edit/:id',  component: TipoBancoEdit,   data: { title: 'Editar Tipo de Banco' } },

    { path: 'cenefco/tipos-universidad',          component: TiposUniversidad,      data: { title: 'Tipos de Universidad' } },
    { path: 'cenefco/tipo-universidad-create',    component: TipoUniversidadCreate, data: { title: 'Nuevo Tipo de Universidad' } },
    { path: 'cenefco/tipo-universidad-edit/:id',  component: TipoUniversidadEdit,   data: { title: 'Editar Tipo de Universidad' } },

    { path: 'cenefco/tipos-postgrado',          component: TiposPostgrado,      data: { title: 'Tipos de Postgrado' } },
    { path: 'cenefco/tipo-postgrado-create',    component: TipoPostgradoCreate, data: { title: 'Nuevo Tipo Postgrado' } },
    { path: 'cenefco/tipo-postgrado-edit/:id',  component: TipoPostgradoEdit,   data: { title: 'Editar Tipo Postgrado' } },

    { path: 'cenefco/configuracion-academica',           component: ConfiguracionAcademica,       data: { title: 'Configuración Académica' } },
    { path: 'cenefco/configuracion-academica-create',    component: ConfiguracionAcademicaCreate, data: { title: 'Nueva Configuración' } },
    { path: 'cenefco/configuracion-academica-edit/:id',  component: ConfiguracionAcademicaEdit,   data: { title: 'Editar Configuración' } },

    { path: 'cenefco/universidades',          component: Universidades,     data: { title: 'Universidades' } },
    { path: 'cenefco/universidades/create',   component: UniversidadCreate, data: { title: 'Nueva Universidad' } },
    { path: 'cenefco/universidades/edit/:id', component: UniversidadEdit,   data: { title: 'Editar Universidad' } },

    { path: 'cenefco/grados-academicos',   component: GradosAcademicos, data: { title: 'Grados Académicos' } },
    { path: 'cenefco/grado-create',        component: GradoCreate,      data: { title: 'Nuevo Grado Académico' } },
    { path: 'cenefco/grado-edit/:id',      component: GradoEdit,        data: { title: 'Editar Grado Académico' } },

    { path: 'cenefco/expedido',          component: Expedido,      data: { title: 'Expedido' } },
    { path: 'cenefco/expedido-create',   component: ExpedidoCreate, data: { title: 'Nuevo Expedido' } },
    { path: 'cenefco/expedido-edit/:id', component: ExpedidoEdit,   data: { title: 'Editar Expedido' } },
]
