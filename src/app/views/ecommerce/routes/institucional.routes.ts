import { Routes } from '@angular/router'

import { Autoridades }      from '../../../autoridades/presentation/autoridades/autoridades'
import { AutoridadCreate }  from '../../../autoridades/presentation/autoridad-create/autoridad-create'
import { AutoridadEdit }    from '../../../autoridades/presentation/autoridad-edit/autoridad-edit'

import { Secretarias }      from '../../../secretarias/presentation/secretarias/secretarias'
import { SecretariaCreate } from '../../../secretarias/presentation/secretaria-create/secretaria-create'
import { SecretariaEdit }   from '../../../secretarias/presentation/secretaria-edit/secretaria-edit'

import { Organigramas }      from '../../../organigramas/presentation/organigramas/organigramas'
import { OrganigramaCreate } from '../../../organigramas/presentation/organigrama-create/organigrama-create'
import { OrganigramaEdit }   from '../../../organigramas/presentation/organigrama-edit/organigrama-edit'

import { DirectorioInstitucionalList }    from '../../../directorio-institucional/presentation/directorio-institucional/directorio-institucional'
import { DirectorioInstitucionalCreate }  from '../../../directorio-institucional/presentation/directorio-institucional-create/directorio-institucional-create'
import { DirectorioInstitucionalEdit }    from '../../../directorio-institucional/presentation/directorio-institucional-edit/directorio-institucional-edit'

import { HistoriaInstitucionalList }    from '../../../historia-institucional/presentation/historia-institucional/historia-institucional'
import { HistoriaInstitucionalCreate }  from '../../../historia-institucional/presentation/historia-institucional-create/historia-institucional-create'
import { HistoriaInstitucionalEdit }    from '../../../historia-institucional/presentation/historia-institucional-edit/historia-institucional-edit'

import { CifrasInstitucionales } from '../../../cifras-institucionales/presentation/cifras-institucionales/cifras-institucionales'
import { CifraCreate }           from '../../../cifras-institucionales/presentation/cifra-create/cifra-create'
import { CifraEdit }             from '../../../cifras-institucionales/presentation/cifra-edit/cifra-edit'

import { HitosInstitucionales } from '../../../hitos-institucionales/presentation/hitos-institucionales/hitos-institucionales'
import { HitoCreate }           from '../../../hitos-institucionales/presentation/hito-create/hito-create'
import { HitoEdit }             from '../../../hitos-institucionales/presentation/hito-edit/hito-edit'

import { Aliados }      from '../../../aliados/presentation/aliados/aliados'
import { AliadoCreate } from '../../../aliados/presentation/aliado-create/aliado-create'
import { AliadoEdit }   from '../../../aliados/presentation/aliado-edit/aliado-edit'

import { Acreditaciones }     from '../../../acreditaciones/presentation/acreditaciones/acreditaciones'
import { AcreditacionCreate } from '../../../acreditaciones/presentation/acreditacion-create/acreditacion-create'
import { AcreditacionEdit }   from '../../../acreditaciones/presentation/acreditacion-edit/acreditacion-edit'

import { NotasPrensa }      from '../../../notas-prensa/presentation/notas-prensa/notas-prensa'
import { NotaPrensaCreate } from '../../../notas-prensa/presentation/nota-prensa-create/nota-prensa-create'
import { NotaPrensaEdit }   from '../../../notas-prensa/presentation/nota-prensa-edit/nota-prensa-edit'

import { Normas }      from '../../../normas/presentation/normas/normas'
import { NormaCreate } from '../../../normas/presentation/norma-create/norma-create'
import { NormaEdit }   from '../../../normas/presentation/norma-edit/norma-edit'

import { TiposNorma }      from '../../../tipos-norma/presentation/tipos-norma/tipos-norma'
import { TipoNormaCreate } from '../../../tipos-norma/presentation/tipo-norma-create/tipo-norma-create'
import { TipoNormaEdit }   from '../../../tipos-norma/presentation/tipo-norma-edit/tipo-norma-edit'

import { ManualesInstitucionales }      from '../../../manuales-institucionales/presentation/manuales-institucionales/manuales-institucionales'
import { ManualInstitucionalCreate }    from '../../../manuales-institucionales/presentation/manual-institucional-create/manual-institucional-create'
import { ManualInstitucionalEdit }      from '../../../manuales-institucionales/presentation/manual-institucional-edit/manual-institucional-edit'

import { DocumentosTransparencia }      from '../../../documentos-transparencia/presentation/documentos-transparencia/documentos-transparencia'
import { DocumentoTransparenciaCreate } from '../../../documentos-transparencia/presentation/documento-transparencia-create/documento-transparencia-create'
import { DocumentoTransparenciaEdit }   from '../../../documentos-transparencia/presentation/documento-transparencia-edit/documento-transparencia-edit'

import { TiposDocumentoTransparencia }      from '../../../tipos-documento-transparencia/presentation/tipos-documento-transparencia/tipos-documento-transparencia'
import { TipoDocumentoTransparenciaCreate } from '../../../tipos-documento-transparencia/presentation/tipo-documento-transparencia-create/tipo-documento-transparencia-create'
import { TipoDocumentoTransparenciaEdit }   from '../../../tipos-documento-transparencia/presentation/tipo-documento-transparencia-edit/tipo-documento-transparencia-edit'

import { TesisList }    from '../../../tesis/presentation/tesis/tesis'
import { TesisCreate }  from '../../../tesis/presentation/tesis-create/tesis-create'
import { TesisEdit }    from '../../../tesis/presentation/tesis-edit/tesis-edit'

import { Monografias }      from '../../../monografias/presentation/monografias/monografias'
import { MonografiaCreate } from '../../../monografias/presentation/monografia-create/monografia-create'
import { MonografiaEdit }   from '../../../monografias/presentation/monografia-edit/monografia-edit'

import { Revistas }      from '../../../revistas/presentation/revistas/revistas'
import { RevistaCreate } from '../../../revistas/presentation/revista-create/revista-create'
import { RevistaEdit }   from '../../../revistas/presentation/revista-edit/revista-edit'

import { RevistasCientificas }      from '../../../revistas-cientificas/presentation/revistas-cientificas/revistas-cientificas'
import { RevistaCientificaCreate }  from '../../../revistas-cientificas/presentation/revista-cientifica-create/revista-cientifica-create'
import { RevistaCientificaEdit }    from '../../../revistas-cientificas/presentation/revista-cientifica-edit/revista-cientifica-edit'

import { Menus }          from '../../../menus/presentation/menus/menus'
import { MenuCreate }     from '../../../menus/presentation/menu-create/menu-create'
import { MenuEdit }       from '../../../menus/presentation/menu-edit/menu-edit'
import { MenuItems }      from '../../../menus/presentation/menu-items/menu-items'
import { MenuItemCreate } from '../../../menus/presentation/menu-item-create/menu-item-create'
import { MenuItemEdit }   from '../../../menus/presentation/menu-item-edit/menu-item-edit'

export const INSTITUCIONAL_ROUTES: Routes = [
    { path: 'cenefco/autoridades',          component: Autoridades,      data: { title: 'Autoridades' } },
    { path: 'cenefco/autoridad-create',     component: AutoridadCreate,  data: { title: 'Nueva Autoridad' } },
    { path: 'cenefco/autoridad-edit/:id',   component: AutoridadEdit,    data: { title: 'Editar Autoridad' } },

    { path: 'cenefco/secretarias',          component: Secretarias,      data: { title: 'Secretarías' } },
    { path: 'cenefco/secretaria-create',    component: SecretariaCreate, data: { title: 'Nueva Secretaría' } },
    { path: 'cenefco/secretaria-edit/:slug', component: SecretariaEdit,   data: { title: 'Editar Secretaría' } },

    { path: 'cenefco/organigramas',          component: Organigramas,      data: { title: 'Organigramas' } },
    { path: 'cenefco/organigrama-create',    component: OrganigramaCreate, data: { title: 'Nuevo Organigrama' } },
    { path: 'cenefco/organigrama-edit/:id',  component: OrganigramaEdit,   data: { title: 'Editar Organigrama' } },

    { path: 'cenefco/directorio-institucional',          component: DirectorioInstitucionalList,   data: { title: 'Directorio Institucional' } },
    { path: 'cenefco/directorio-institucional-create',   component: DirectorioInstitucionalCreate, data: { title: 'Nueva Entrada del Directorio' } },
    { path: 'cenefco/directorio-institucional-edit/:id', component: DirectorioInstitucionalEdit,   data: { title: 'Editar Entrada del Directorio' } },

    { path: 'cenefco/historia-institucional',          component: HistoriaInstitucionalList,   data: { title: 'Historia Institucional' } },
    { path: 'cenefco/historia-institucional-create',   component: HistoriaInstitucionalCreate, data: { title: 'Nueva Entrada de Historia' } },
    { path: 'cenefco/historia-institucional-edit/:id', component: HistoriaInstitucionalEdit,   data: { title: 'Editar Entrada de Historia' } },

    { path: 'cenefco/cifras-institucionales', component: CifrasInstitucionales, data: { title: 'Cifras Institucionales' } },
    { path: 'cenefco/cifra-create',           component: CifraCreate,           data: { title: 'Nueva Cifra Institucional' } },
    { path: 'cenefco/cifra-edit/:id',         component: CifraEdit,             data: { title: 'Editar Cifra Institucional' } },

    { path: 'cenefco/hitos-institucionales', component: HitosInstitucionales, data: { title: 'Hitos Institucionales' } },
    { path: 'cenefco/hito-create',           component: HitoCreate,           data: { title: 'Nuevo Hito Institucional' } },
    { path: 'cenefco/hito-edit/:id',         component: HitoEdit,             data: { title: 'Editar Hito Institucional' } },

    { path: 'cenefco/aliados',          component: Aliados,      data: { title: 'Aliados' } },
    { path: 'cenefco/aliado-create',    component: AliadoCreate, data: { title: 'Nuevo Aliado' } },
    { path: 'cenefco/aliado-edit/:id',  component: AliadoEdit,   data: { title: 'Editar Aliado' } },

    { path: 'cenefco/acreditaciones',          component: Acreditaciones,     data: { title: 'Acreditaciones' } },
    { path: 'cenefco/acreditacion-create',     component: AcreditacionCreate, data: { title: 'Nueva Acreditación' } },
    { path: 'cenefco/acreditacion-edit/:id',   component: AcreditacionEdit,   data: { title: 'Editar Acreditación' } },

    { path: 'cenefco/notas-prensa',          component: NotasPrensa,      data: { title: 'Notas de Prensa' } },
    { path: 'cenefco/nota-prensa-create',    component: NotaPrensaCreate, data: { title: 'Nueva Nota de Prensa' } },
    { path: 'cenefco/nota-prensa-edit/:id',  component: NotaPrensaEdit,   data: { title: 'Editar Nota de Prensa' } },

    { path: 'cenefco/normas',          component: Normas,      data: { title: 'Normas' } },
    { path: 'cenefco/norma-create',    component: NormaCreate, data: { title: 'Nueva Norma' } },
    { path: 'cenefco/norma-edit/:id',  component: NormaEdit,   data: { title: 'Editar Norma' } },

    { path: 'cenefco/tipos-norma',          component: TiposNorma,      data: { title: 'Tipos de Norma' } },
    { path: 'cenefco/tipo-norma-create',    component: TipoNormaCreate, data: { title: 'Nuevo Tipo de Norma' } },
    { path: 'cenefco/tipo-norma-edit/:id',  component: TipoNormaEdit,   data: { title: 'Editar Tipo de Norma' } },

    { path: 'cenefco/manuales-institucionales',          component: ManualesInstitucionales,   data: { title: 'Manuales Institucionales' } },
    { path: 'cenefco/manual-institucional-create',       component: ManualInstitucionalCreate, data: { title: 'Nuevo Manual Institucional' } },
    { path: 'cenefco/manual-institucional-edit/:id',     component: ManualInstitucionalEdit,   data: { title: 'Editar Manual Institucional' } },

    { path: 'cenefco/documentos-transparencia',          component: DocumentosTransparencia,      data: { title: 'Documentos de Transparencia' } },
    { path: 'cenefco/documento-transparencia-create',    component: DocumentoTransparenciaCreate, data: { title: 'Nuevo Documento de Transparencia' } },
    { path: 'cenefco/documento-transparencia-edit/:id',  component: DocumentoTransparenciaEdit,   data: { title: 'Editar Documento de Transparencia' } },

    { path: 'cenefco/tipos-documento-transparencia',          component: TiposDocumentoTransparencia,      data: { title: 'Tipos de Documento de Transparencia' } },
    { path: 'cenefco/tipo-documento-transparencia-create',    component: TipoDocumentoTransparenciaCreate, data: { title: 'Nuevo Tipo de Documento' } },
    { path: 'cenefco/tipo-documento-transparencia-edit/:id',  component: TipoDocumentoTransparenciaEdit,   data: { title: 'Editar Tipo de Documento' } },

    { path: 'cenefco/tesis',          component: TesisList,   data: { title: 'Tesis' } },
    { path: 'cenefco/tesis-create',   component: TesisCreate, data: { title: 'Nueva Tesis' } },
    { path: 'cenefco/tesis-edit/:id', component: TesisEdit,   data: { title: 'Editar Tesis' } },

    { path: 'cenefco/monografias',          component: Monografias,      data: { title: 'Monografías' } },
    { path: 'cenefco/monografia-create',    component: MonografiaCreate, data: { title: 'Nueva Monografía' } },
    { path: 'cenefco/monografia-edit/:id',  component: MonografiaEdit,   data: { title: 'Editar Monografía' } },

    { path: 'cenefco/revistas',          component: Revistas,      data: { title: 'Revistas' } },
    { path: 'cenefco/revista-create',    component: RevistaCreate, data: { title: 'Nueva Revista' } },
    { path: 'cenefco/revista-edit/:id',  component: RevistaEdit,   data: { title: 'Editar Revista' } },

    { path: 'cenefco/revistas-cientificas',          component: RevistasCientificas,     data: { title: 'Revistas Científicas' } },
    { path: 'cenefco/revista-cientifica-create',     component: RevistaCientificaCreate, data: { title: 'Nueva Revista Científica' } },
    { path: 'cenefco/revista-cientifica-edit/:id',   component: RevistaCientificaEdit,   data: { title: 'Editar Revista Científica' } },

    { path: 'cenefco/menus',               component: Menus,          data: { title: 'Menús del Portal' } },
    { path: 'cenefco/menu-create',         component: MenuCreate,     data: { title: 'Nuevo Menú' } },
    { path: 'cenefco/menu-edit/:id',       component: MenuEdit,       data: { title: 'Editar Menú' } },
    { path: 'cenefco/menu-items/:menuId',  component: MenuItems,      data: { title: 'Ítems de Menú' } },
    { path: 'cenefco/menu-item-create/:menuId', component: MenuItemCreate, data: { title: 'Nuevo Ítem de Menú' } },
    { path: 'cenefco/menu-item-edit/:id',  component: MenuItemEdit,   data: { title: 'Editar Ítem de Menú' } },
]
