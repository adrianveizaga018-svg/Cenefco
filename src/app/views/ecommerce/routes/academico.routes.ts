import { Routes } from '@angular/router'

import { Cursos }      from '../../../cursos/presentation/cursos/cursos'
import { CursoCreate } from '../../../cursos/presentation/curso-create/curso-create'
import { CursoEdit }   from '../../../cursos/presentation/curso-edit/curso-edit'

import { Areas }      from '../../../areas/presentation/areas/areas'
import { AreaCreate } from '../../../areas/presentation/area-create/area-create'
import { AreaEdit }   from '../../../areas/presentation/area-edit/area-edit'

import { CategoriasProgramas }     from '../../../categorias-programa/presentation/categorias-programa/categorias-programa'
import { CategoriaProgramaCreate } from '../../../categorias-programa/presentation/categoria-programa-create/categoria-programa-create'
import { CategoriaProgramaEdit }   from '../../../categorias-programa/presentation/categoria-programa-edit/categoria-programa-edit'

import { PlanesAcademicos } from '../../../planes-academicos/presentation/planes-academicos/planes-academicos'
import { PlanCreate }       from '../../../planes-academicos/presentation/plan-create/plan-create'
import { PlanEdit }         from '../../../planes-academicos/presentation/plan-edit/plan-edit'

import { ProgramasAcademicos }     from '../../../programas-academicos/presentation/programas-academicos/programas-academicos'
import { ProgramaAcademicoCreate } from '../../../programas-academicos/presentation/programa-academico-create/programa-academico-create'
import { ProgramaAcademicoEdit }   from '../../../programas-academicos/presentation/programa-academico-edit/programa-academico-edit'

import { Convenios }      from '../../../convenios/presentation/convenios/convenios'
import { ConvenioCreate } from '../../../convenios/presentation/convenio-create/convenio-create'
import { ConvenioEdit }   from '../../../convenios/presentation/convenio-edit/convenio-edit'

import { InscripcionesDiplomado }     from '../../../inscripciones-diplomado/presentation/inscripciones-diplomado/inscripciones-diplomado'
import { InscripcionDiplomadoDetail } from '../../../inscripciones-diplomado/presentation/inscripcion-diplomado-detail/inscripcion-diplomado-detail'

import { DirectorioCursos }               from '../../../directorio-archivos/presentation/directorio-cursos/directorio-cursos'
import { DirectorioParticipantes }        from '../../../directorio-archivos/presentation/directorio-participantes/directorio-participantes'
import { DirectorioArchivosParticipante } from '../../../directorio-archivos/presentation/directorio-archivos-participante/directorio-archivos-participante'

import { Inscripciones }         from '../../../inscripciones/presentation/inscripciones/inscripciones'
import { InscripcionCreate }     from '../../../inscripciones/presentation/inscripcion-create/inscripcion-create'
import { InscripcionEdit }       from '../../../inscripciones/presentation/inscripcion-edit/inscripcion-edit'
import { InscripcionDetail }     from '../../../inscripciones/presentation/inscripcion-detail/inscripcion-detail'
import { InscripcionReportes }   from '../../../inscripciones/presentation/inscripcion-reportes/inscripcion-reportes'

import { PagosAcademicos } from '../../../pagos-academicos/presentation/pagos-academicos/pagos-academicos'
import { PagoCreate }      from '../../../pagos-academicos/presentation/pago-create/pago-create'
import { PagoEdit }        from '../../../pagos-academicos/presentation/pago-edit/pago-edit'
import { FechasPago }      from '../../../fechas-pago/presentation/fechas-pago/fechas-pago'
import { FechaPagoCreate } from '../../../fechas-pago/presentation/fecha-pago-create/fecha-pago-create'
import { FechaPagoEdit }   from '../../../fechas-pago/presentation/fecha-pago-edit/fecha-pago-edit'

import { DocentesPerfil }         from '../../../docentes-perfil/presentation/docentes-perfil/docentes-perfil'
import { DocentePerfilCreate }    from '../../../docentes-perfil/presentation/docente-perfil-create/docente-perfil-create'
import { DocentePerfilEdit }      from '../../../docentes-perfil/presentation/docente-perfil-edit/docente-perfil-edit'
import { DocenteReporteMaterias } from '../../../docentes-perfil/presentation/docente-reporte-materias/docente-reporte-materias'

import { SueldosDocentes }             from '../../../sueldos-docentes/presentation/sueldos-docentes/sueldos-docentes'
import { SueldoDocenteCreate }         from '../../../sueldos-docentes/presentation/sueldo-docente-create/sueldo-docente-create'
import { SueldoDocenteDetalleComponent } from '../../../sueldos-docentes/presentation/sueldo-docente-detalle/sueldo-docente-detalle'
import { SueldoDocenteEdit }           from '../../../sueldos-docentes/presentation/sueldo-docente-edit/sueldo-docente-edit'
import { PagoSueldoCreate }            from '../../../sueldos-docentes/presentation/pago-sueldo-create/pago-sueldo-create'

import { ComisionesGenerar }   from '../../../comisiones/presentation/comisiones-generar/comisiones-generar'
import { ComisionesHistorial } from '../../../comisiones/presentation/comisiones-historial/comisiones-historial'

import { CompromisosCobro } from '../../../compromisos-cobro/presentation/compromisos-cobro/compromisos-cobro'

import { Certificados }        from '../../../certificados/presentation/certificados/certificados'
import { CertPlantillas }      from '../../../certificados/presentation/cert-plantillas/cert-plantillas'
import { CertPlantillaCampos } from '../../../certificados/presentation/cert-plantilla-campos/cert-plantilla-campos'
import { ListaAprobados }      from '../../../certificados/presentation/lista-aprobados/lista-aprobados'
import { CertVerificaciones }  from '../../../cert-verificaciones/presentation/cert-verificaciones/cert-verificaciones'
import { CertificadosMasivos } from '../../../certificados/presentation/certificados-masivos/certificados-masivos'

import { DocumentosAcademicos } from '../../../documentos-academicos/presentation/documentos-academicos/documentos-academicos'
import { DocumentoCreate }      from '../../../documentos-academicos/presentation/documento-create/documento-create'
import { DocumentoEdit }        from '../../../documentos-academicos/presentation/documento-edit/documento-edit'

import { CalendarioAcademico } from '../../../calendario-academico/presentation/calendario-academico/calendario-academico'
import { CalendarioCreate }    from '../../../calendario-academico/presentation/calendario-create/calendario-create'
import { CalendarioEdit }      from '../../../calendario-academico/presentation/calendario-edit/calendario-edit'

import { CursosMigrados }        from '../../../cursos-migrados/presentation/cursos-migrados/cursos-migrados'
import { CursoMigradoCreate }    from '../../../cursos-migrados/presentation/curso-migrado-create/curso-migrado-create'
import { CursoMigradoDetail }    from '../../../cursos-migrados/presentation/curso-migrado-detail/curso-migrado-detail'
import { CursoMigradoImportar }  from '../../../cursos-migrados/presentation/curso-migrado-importar/curso-migrado-importar'

import { CitasAsesoria }    from '../../../citas-asesoria/presentation/citas-asesoria/citas-asesoria'
import { CitaAsesoriaEdit } from '../../../citas-asesoria/presentation/cita-asesoria-edit/cita-asesoria-edit'

import { Formularios }      from '../../../formularios/presentation/formularios/formularios'
import { FormularioCreate } from '../../../formularios/presentation/formulario-create/formulario-create'
import { FormularioEdit }   from '../../../formularios/presentation/formulario-edit/formulario-edit'

import { GeneradorFormularios }       from '../../../generador-formularios/presentation/generador-formularios/generador-formularios'
import { GeneradorFormularioCreate }  from '../../../generador-formularios/presentation/generador-formulario-create/generador-formulario-create'
import { GeneradorFormularioEdit }    from '../../../generador-formularios/presentation/generador-formulario-edit/generador-formulario-edit'
import { GeneradorFormularioCampos }  from '../../../generador-formularios/presentation/generador-formulario-campos/generador-formulario-campos'

import { CertConfigProgramas }    from '../../../cert-config-programas/presentation/cert-config-programas/cert-config-programas'
import { CertConfigProgramaEdit } from '../../../cert-config-programas/presentation/cert-config-programa-edit/cert-config-programa-edit'
import { CertSolicitudes }        from '../../../cert-config-programas/presentation/cert-solicitudes/cert-solicitudes'

import { Ingresos }       from '../../../ingresos/presentation/ingresos/ingresos'
import { Ventas }         from '../../../ventas/presentation/ventas/ventas'
import { VentaDetalle }   from '../../../ventas/presentation/venta-detalle/venta-detalle'
import { NuevaVenta }     from '../../../ventas/presentation/nueva-venta/nueva-venta'
import { ReporteVentas }  from '../../../ventas/presentation/reporte-ventas/reporte-ventas'

import { CorreosEnviados } from '../../../correos-enviados/presentation/correos-enviados/correos-enviados'

import { ReporteCobros } from '../../../reportes-cobros/presentation/reporte-cobros/reporte-cobros'

import { Gastos }            from '../../../gastos/presentation/gastos/gastos'
import { GastoEdit }         from '../../../gastos/presentation/gasto-edit/gasto-edit'
import { GastosRecurrentes } from '../../../gastos/presentation/gastos-recurrentes/gastos-recurrentes'

import { CampanasPublicidad } from '../../../campanas-publicidad/presentation/campanas-publicidad/campanas-publicidad'
import { CampanaEdit }        from '../../../campanas-publicidad/presentation/campana-edit/campana-edit'
import { CampanaDetail }      from '../../../campanas-publicidad/presentation/campana-detail/campana-detail'
import { CampanasReporte }    from '../../../campanas-publicidad/presentation/campanas-reporte/campanas-reporte'

import { CampanasLeads }    from '../../../campanas-leads/presentation/campanas-leads/campanas-leads'
import { CampanaLeadEdit }   from '../../../campanas-leads/presentation/campana-lead-edit/campana-lead-edit'
import { CampanaLeadDetail } from '../../../campanas-leads/presentation/campana-lead-detail/campana-lead-detail'
import { LeadDetail }        from '../../../campanas-leads/presentation/lead-detail/lead-detail'

import { Empleados }       from '../../../empleados/presentation/empleados/empleados'
import { EmpleadoEdit }    from '../../../empleados/presentation/empleado-edit/empleado-edit'
import { GenerarPlanilla } from '../../../planillas/presentation/generar-planilla/generar-planilla'
import { AjustesSueldo } from '../../../ajustes-sueldo/presentation/ajustes-sueldo/ajustes-sueldo'
import { Planillas }       from '../../../planillas/presentation/planillas/planillas'

import { DashboardGastos } from '../../../dashboard-gastos/presentation/dashboard-gastos/dashboard-gastos'

import { ConfigHonorarios } from '../../../honorarios/presentation/config-honorarios/config-honorarios'
import { HonorariosDelMes } from '../../../honorarios/presentation/honorarios-del-mes/honorarios-del-mes'

export const ACADEMICO_ROUTES: Routes = [
    { path: 'cenefco/cursos',         component: Cursos,      data: { title: 'Programas' } },
    { path: 'cenefco/curso-create',   component: CursoCreate, data: { title: 'Nuevo Programa' } },
    { path: 'cenefco/curso-edit/:slug', component: CursoEdit,   data: { title: 'Editar Programa' } },

    { path: 'cenefco/control-academico', loadComponent: () => import('../../../control-academico/presentation/control-academico/control-academico'), data: { title: 'Control Académico' } },
    { path: 'cenefco/catalogo-requisitos', loadComponent: () => import('../../../control-academico/presentation/catalogo-requisitos/catalogo-requisitos').then(m => m.CatalogoRequisitosComponent), data: { title: 'Catálogo de Requisitos' } },
    { path: 'cenefco/control-academico/:id', loadComponent: () => import('../../../control-academico/presentation/curso-tareas-detail/curso-tareas-detail'), data: { title: 'Tareas del Curso' } },

    { path: 'cenefco/cursos-migrados',             component: CursosMigrados,      data: { title: 'Cursos Migrados' } },
    { path: 'cenefco/curso-migrado-create',        component: CursoMigradoCreate,  data: { title: 'Nuevo Curso Migrado' } },
    { path: 'cenefco/curso-migrado-importar',      component: CursoMigradoImportar,data: { title: 'Importar Cursos JSON' } },
    { path: 'cenefco/curso-migrado/:id',           component: CursoMigradoDetail,  data: { title: 'Detalle Curso Migrado' } },

    { path: 'cenefco/areas',         component: Areas,      data: { title: 'Áreas' } },
    { path: 'cenefco/area-create',   component: AreaCreate, data: { title: 'Nueva Área' } },
    { path: 'cenefco/area-edit/:slug', component: AreaEdit,   data: { title: 'Editar Área' } },

    { path: 'cenefco/categorias-programa',          component: CategoriasProgramas,     data: { title: 'Categorías de Programa' } },
    { path: 'cenefco/categoria-programa-create',    component: CategoriaProgramaCreate, data: { title: 'Nueva Categoría de Programa' } },
    { path: 'cenefco/categoria-programa-edit/:id',  component: CategoriaProgramaEdit,   data: { title: 'Editar Categoría de Programa' } },

    { path: 'cenefco/planes-academicos',   component: PlanesAcademicos, data: { title: 'Planes Académicos' } },
    { path: 'cenefco/plan-create',         component: PlanCreate,       data: { title: 'Nuevo Plan' } },
    { path: 'cenefco/plan-edit/:id',       component: PlanEdit,         data: { title: 'Editar Plan' } },

    { path: 'cenefco/programas-academicos',          component: ProgramasAcademicos,     data: { title: 'Programas Académicos' } },
    { path: 'cenefco/programa-academico-create',     component: ProgramaAcademicoCreate, data: { title: 'Nuevo Programa Académico' } },
    { path: 'cenefco/programa-academico-edit/:id',   component: ProgramaAcademicoEdit,   data: { title: 'Editar Programa Académico' } },

    { path: 'cenefco/convenios',          component: Convenios,      data: { title: 'Convenios' } },
    { path: 'cenefco/convenio-create',    component: ConvenioCreate, data: { title: 'Nuevo Convenio' } },
    { path: 'cenefco/convenio-edit/:id',  component: ConvenioEdit,   data: { title: 'Editar Convenio' } },

    { path: 'cenefco/inscripciones-diplomado',          component: InscripcionesDiplomado,     data: { title: 'Inscripciones a Diplomado' } },
    { path: 'cenefco/inscripcion-diplomado-detail/:id', component: InscripcionDiplomadoDetail, data: { title: 'Detalle de Inscripción a Diplomado' } },

    { path: 'cenefco/directorio-archivos',                                             component: DirectorioCursos,               data: { title: 'Directorio de Archivos' } },
    { path: 'cenefco/directorio-archivos/:idImp/participantes',                        component: DirectorioParticipantes,        data: { title: 'Participantes' } },
    { path: 'cenefco/directorio-archivos/:idImp/participantes/:idIns/archivos',        component: DirectorioArchivosParticipante, data: { title: 'Archivos del Participante' } },

    { path: 'cenefco/inscripciones',           component: Inscripciones,      data: { title: 'Inscripciones' } },
    { path: 'cenefco/inscripciones/reportes', component: InscripcionReportes, data: { title: 'Reportes de Inscripciones' } },
    { path: 'cenefco/reporte-cobros',         component: ReporteCobros,       data: { title: 'Reporte de Cobros' } },
    { path: 'cenefco/inscripcion-create',      component: InscripcionCreate,  data: { title: 'Nueva Inscripción' } },
    { path: 'cenefco/inscripcion-edit/:id',    component: InscripcionEdit,    data: { title: 'Editar Inscripción' } },
    { path: 'cenefco/inscripcion-detail/:id',  component: InscripcionDetail,  data: { title: 'Detalle de Inscripción' } },

    { path: 'cenefco/pagos-academicos',    component: PagosAcademicos, data: { title: 'Pagos Académicos' } },
    { path: 'cenefco/pago-create',         component: PagoCreate,      data: { title: 'Registrar Pago' } },
    { path: 'cenefco/pago-edit/:id',       component: PagoEdit,        data: { title: 'Editar Pago' } },
    { path: 'cenefco/fechas-pago',         component: FechasPago,      data: { title: 'Fechas de Pago' } },
    { path: 'cenefco/fecha-pago-create',   component: FechaPagoCreate, data: { title: 'Nueva Fecha de Pago' } },
    { path: 'cenefco/fecha-pago-edit/:id', component: FechaPagoEdit,   data: { title: 'Editar Fecha de Pago' } },

    { path: 'cenefco/docentes-perfil',              component: DocentesPerfil,         data: { title: 'Docentes' } },
    { path: 'cenefco/docente-perfil-create',        component: DocentePerfilCreate,    data: { title: 'Nuevo Docente' } },
    { path: 'cenefco/docente-perfil-edit/:slug',    component: DocentePerfilEdit,      data: { title: 'Editar Docente' } },
    { path: 'cenefco/docente-reporte-materias',     component: DocenteReporteMaterias, data: { title: 'Reporte Materias por Docente' } },

    { path: 'cenefco/sueldos-docentes',             component: SueldosDocentes,               data: { title: 'Sueldos Docentes' } },
    { path: 'cenefco/sueldos-docentes/create',      component: SueldoDocenteCreate,           data: { title: 'Nuevo Sueldo Docente' } },
    { path: 'cenefco/sueldos-docentes/:id',         component: SueldoDocenteDetalleComponent, data: { title: 'Detalle Sueldo Docente' } },
    { path: 'cenefco/sueldos-docentes/:id/edit',    component: SueldoDocenteEdit,             data: { title: 'Editar Sueldo Docente' } },
    { path: 'cenefco/sueldos-docentes/:id/pagar',   component: PagoSueldoCreate,              data: { title: 'Registrar Pago' } },

    { path: 'cenefco/comisiones',            component: ComisionesHistorial, data: { title: 'Comisiones a Vendedores' } },
    { path: 'cenefco/comisiones/generar',    component: ComisionesGenerar,   data: { title: 'Generar Comisión' } },

    { path: 'cenefco/compromisos-cobro',     component: CompromisosCobro,    data: { title: 'Compromisos de Cobro' } },

    { path: 'cenefco/certificados',               component: Certificados,        data: { title: 'Certificados' } },
    { path: 'cenefco/certificados-masivos',        component: CertificadosMasivos, data: { title: 'Generación Masiva PDF' } },
    { path: 'cenefco/cert-plantillas',            component: CertPlantillas,      data: { title: 'Plantillas de Certificado' } },
    { path: 'cenefco/cert-plantilla-campos/:id',  component: CertPlantillaCampos, data: { title: 'Campos de Plantilla' } },
    { path: 'cenefco/lista-aprobados',            component: ListaAprobados,      data: { title: 'Lista de Aprobados' } },
    { path: 'cenefco/cert-verificaciones',        component: CertVerificaciones,  data: { title: 'Verificaciones de Certificados' } },
    { path: 'cenefco/cert-config-programas',              component: CertConfigProgramas,    data: { title: 'Certificados Post-Inscripción' } },
    { path: 'cenefco/cert-config-programa-edit/:id',      component: CertConfigProgramaEdit, data: { title: 'Configurar Certificados' } },
    { path: 'cenefco/cert-solicitudes',                   component: CertSolicitudes,        data: { title: 'Solicitudes de Certificados' } },

    { path: 'cenefco/documentos-academicos',   component: DocumentosAcademicos, data: { title: 'Documentos Académicos' } },
    { path: 'cenefco/documento-create',        component: DocumentoCreate,      data: { title: 'Nuevo Documento' } },
    { path: 'cenefco/documento-edit/:id',      component: DocumentoEdit,        data: { title: 'Editar Documento' } },

    { path: 'cenefco/calendario-academico',   component: CalendarioAcademico, data: { title: 'Calendario Académico' } },
    { path: 'cenefco/calendario-create',      component: CalendarioCreate,    data: { title: 'Nuevo Evento de Calendario' } },
    { path: 'cenefco/calendario-edit/:id',    component: CalendarioEdit,      data: { title: 'Editar Evento de Calendario' } },

    { path: 'cenefco/citas-asesoria',          component: CitasAsesoria,    data: { title: 'Citas de Asesoría' } },
    { path: 'cenefco/citas-asesoria/edit/:id', component: CitaAsesoriaEdit, data: { title: 'Editar Cita de Asesoría' } },

    { path: 'cenefco/formularios',          component: Formularios,      data: { title: 'Formularios de Inscripción' } },
    { path: 'cenefco/formulario-create',    component: FormularioCreate, data: { title: 'Nuevo Formulario' } },
    { path: 'cenefco/formulario-edit/:id',  component: FormularioEdit,   data: { title: 'Editar Formulario' } },

    { path: 'cenefco/generador-formularios',              component: GeneradorFormularios,      data: { title: 'Generador de Formularios' } },
    { path: 'cenefco/generador-formulario-create',        component: GeneradorFormularioCreate, data: { title: 'Nuevo Formulario' } },
    { path: 'cenefco/generador-formulario-edit/:id',      component: GeneradorFormularioEdit,   data: { title: 'Editar Formulario' } },
    { path: 'cenefco/generador-formulario-campos/:id',    component: GeneradorFormularioCampos, data: { title: 'Campos de Formulario' } },

    { path: 'cenefco/ingresos',             component: Ingresos,       data: { title: 'Ingresos' } },
    { path: 'cenefco/ventas',              component: Ventas,         data: { title: 'Ventas' } },
    { path: 'cenefco/nueva-venta',         component: NuevaVenta,     data: { title: 'Nueva Venta' } },
    { path: 'cenefco/venta-detalle/:id',   component: VentaDetalle,   data: { title: 'Detalle de Venta' } },
    { path: 'cenefco/reporte-financiero',  component: ReporteVentas,  data: { title: 'Reporte Financiero' } },
    { path: 'cenefco/correos-enviados',    component: CorreosEnviados, data: { title: 'Correos Enviados' } },

    { path: 'cenefco/gastos',              component: Gastos,            data: { title: 'Gastos' } },
    { path: 'cenefco/gasto-create',        component: GastoEdit,         data: { title: 'Registrar Gasto' } },
    { path: 'cenefco/gasto-edit/:id',      component: GastoEdit,         data: { title: 'Editar Gasto' } },
    { path: 'cenefco/gastos-recurrentes',  component: GastosRecurrentes, data: { title: 'Gastos Recurrentes' } },

    { path: 'cenefco/campanas-publicidad', component: CampanasPublicidad, data: { title: 'Campañas Publicitarias' } },
    { path: 'cenefco/campana-create',      component: CampanaEdit,        data: { title: 'Nueva Campaña Publicitaria' } },
    { path: 'cenefco/campana-edit/:id',    component: CampanaEdit,        data: { title: 'Editar Campaña Publicitaria' } },
    { path: 'cenefco/campana-detail/:id',  component: CampanaDetail,      data: { title: 'Detalle de Campaña' } },
    { path: 'cenefco/campanas-reporte',    component: CampanasReporte,    data: { title: 'Reporte de Campañas' } },

    { path: 'cenefco/campanas-leads',        component: CampanasLeads,      data: { title: 'Campañas de Leads' } },
    { path: 'cenefco/campana-lead-create',   component: CampanaLeadEdit,    data: { title: 'Nueva Campaña de Leads' } },
    { path: 'cenefco/campana-lead-edit/:id', component: CampanaLeadEdit,    data: { title: 'Editar Campaña de Leads' } },
    { path: 'cenefco/campana-lead-detail/:id', component: CampanaLeadDetail, data: { title: 'Detalle de Campaña de Leads' } },
    { path: 'cenefco/campana-lead-detail/:campanaId/lead/:leadId', component: LeadDetail, data: { title: 'CRM - Detalle de Lead' } },

    { path: 'cenefco/empleados',           component: Empleados,       data: { title: 'Empleados de Planta' } },
    { path: 'cenefco/empleado-create',     component: EmpleadoEdit,    data: { title: 'Nuevo Empleado' } },
    { path: 'cenefco/empleado-edit/:id',   component: EmpleadoEdit,    data: { title: 'Editar Empleado' } },
    { path: 'cenefco/generar-planilla',    component: GenerarPlanilla, data: { title: 'Generar Planilla' } },
    { path: 'cenefco/ajustes-sueldo',      component: AjustesSueldo,   data: { title: 'Ajustes de Sueldo' } },
    { path: 'cenefco/planillas',           component: Planillas,       data: { title: 'Historial de Planillas' } },

    { path: 'cenefco/dashboard-gastos',    component: DashboardGastos, data: { title: 'Dashboard de Gastos' } },

    { path: 'cenefco/config-honorarios',  component: ConfigHonorarios, data: { title: 'Configurar Honorarios' } },
    { path: 'cenefco/honorarios-mes',     component: HonorariosDelMes, data: { title: 'Honorarios del Mes' } },

    { path: 'estudiante/dashboard',        loadComponent: () => import('../../../estudiante/presentation/estudiante-dashboard/estudiante-dashboard').then(m => m.EstudianteDashboardComponent), data: { title: 'Mi Panel' } },
    { path: 'estudiante/mis-cursos',       loadComponent: () => import('../../../estudiante/presentation/mis-cursos/mis-cursos').then(m => m.MisCursosComponent), data: { title: 'Mis Cursos' } },
    { path: 'estudiante/mis-certificados',  loadComponent: () => import('../../../estudiante/presentation/mis-certificados/mis-certificados').then(m => m.MisCertificadosComponent), data: { title: 'Mis Certificados' } },
    { path: 'estudiante/mis-pagos',        loadComponent: () => import('../../../estudiante/presentation/mis-pagos/mis-pagos').then(m => m.MisPagosEstudianteComponent), data: { title: 'Mis Pagos' } },
    { path: 'estudiante/catalogo',         loadComponent: () => import('../../../estudiante/presentation/oferta-academica/oferta-academica').then(m => m.OfertaAcademicaComponent), data: { title: 'Catálogo de Cursos' } },
]
