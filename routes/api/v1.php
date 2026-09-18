<?php

use App\Http\Controllers\Api\AjusteController;
use App\Http\Controllers\Api\BannerPortalController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EtiquetaController;
use App\Http\Controllers\Api\EventoController;
use App\Http\Controllers\Api\MensajeContactoController;
use App\Http\Controllers\Api\RedSocialController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\WhatsAppAdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'stats'])
        ->middleware('permiso:reportes.ver');

    Route::get('/dashboard/gerencia', [DashboardController::class, 'gerencia'])
        ->middleware('permiso:reportes.ver');

    Route::post('/upload/image', [UploadController::class, 'image'])
        ->middleware('permiso:web.crear');
    Route::post('/upload/file', [UploadController::class, 'file'])
        ->middleware('permiso:web.crear');

    Route::get('/redes-sociales', [RedSocialController::class, 'index'])
        ->middleware('permiso:web.ver');
    Route::post('/redes-sociales', [RedSocialController::class, 'store'])
        ->middleware('permiso:web.crear');
    Route::get('/redes-sociales/{id}', [RedSocialController::class, 'show'])
        ->middleware('permiso:web.ver');
    Route::put('/redes-sociales/{id}', [RedSocialController::class, 'update'])
        ->middleware('permiso:web.editar');
    Route::delete('/redes-sociales/{id}', [RedSocialController::class, 'destroy'])
        ->middleware('permiso:web.eliminar');

    Route::get('/usuarios', [\App\Http\Controllers\Api\UsuarioController::class, 'index'])
        ->middleware('permiso:usuarios.ver');
    Route::post('/usuarios', [\App\Http\Controllers\Api\UsuarioController::class, 'store'])
        ->middleware('permiso:usuarios.crear');
    Route::get('/usuarios/{id}', [\App\Http\Controllers\Api\UsuarioController::class, 'show'])
        ->middleware('permiso:usuarios.ver');
    Route::put('/usuarios/{id}', [\App\Http\Controllers\Api\UsuarioController::class, 'update'])
        ->middleware('permiso:usuarios.editar');
    Route::delete('/usuarios/{id}', [\App\Http\Controllers\Api\UsuarioController::class, 'destroy'])
        ->middleware('permiso:usuarios.eliminar');

    Route::get('/roles', [RoleController::class, 'index'])
        ->middleware('permiso:usuarios.ver');
    Route::get('/roles/{id}', [RoleController::class, 'show'])
        ->middleware('permiso:usuarios.ver');
    Route::post('/roles', [RoleController::class, 'store'])
        ->middleware('permiso:usuarios.crear');
    Route::put('/roles/{id}', [RoleController::class, 'update'])
        ->middleware('permiso:usuarios.editar');
    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])
        ->middleware('permiso:usuarios.eliminar');

    Route::get('/etiquetas', [EtiquetaController::class, 'index'])
        ->middleware('permiso:noticias.ver');
    Route::post('/etiquetas', [EtiquetaController::class, 'store'])
        ->middleware('permiso:noticias.crear');
    Route::get('/etiquetas/slug/{slug}', [EtiquetaController::class, 'showBySlug'])
        ->middleware('permiso:noticias.ver');
    Route::get('/etiquetas/{id}', [EtiquetaController::class, 'show'])
        ->middleware('permiso:noticias.ver');
    Route::put('/etiquetas/{id}', [EtiquetaController::class, 'update'])
        ->middleware('permiso:noticias.editar');
    Route::delete('/etiquetas/{id}', [EtiquetaController::class, 'destroy'])
        ->middleware('permiso:noticias.eliminar');

    Route::get('/eventos', [EventoController::class, 'index'])
        ->middleware('permiso:eventos.ver');
    Route::post('/eventos', [EventoController::class, 'store'])
        ->middleware('permiso:eventos.crear');
    Route::get('/eventos/slug/{slug}', [EventoController::class, 'showBySlug'])
        ->middleware('permiso:eventos.ver');
    Route::get('/eventos/{id}', [EventoController::class, 'show'])
        ->middleware('permiso:eventos.ver');
    Route::put('/eventos/{id}', [EventoController::class, 'update'])
        ->middleware('permiso:eventos.editar');
    Route::delete('/eventos/{id}', [EventoController::class, 'destroy'])
        ->middleware('permiso:eventos.eliminar');

    Route::get('/preguntas-frecuentes', [\App\Http\Controllers\Api\PreguntaFrecuenteController::class, 'index'])
        ->middleware('permiso:contenido.ver');
    Route::post('/preguntas-frecuentes', [\App\Http\Controllers\Api\PreguntaFrecuenteController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/preguntas-frecuentes/{id}', [\App\Http\Controllers\Api\PreguntaFrecuenteController::class, 'show'])
        ->middleware('permiso:contenido.ver');
    Route::put('/preguntas-frecuentes/{id}', [\App\Http\Controllers\Api\PreguntaFrecuenteController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/preguntas-frecuentes/{id}', [\App\Http\Controllers\Api\PreguntaFrecuenteController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/mensajes-contacto', [MensajeContactoController::class, 'index'])
        ->middleware('permiso:contacto.ver');
    Route::post('/mensajes-contacto', [MensajeContactoController::class, 'store'])
        ->middleware('permiso:contacto.crear');
    Route::get('/mensajes-contacto/{id}', [MensajeContactoController::class, 'show'])
        ->middleware('permiso:contacto.ver');
    Route::post('/mensajes-contacto/{id}/responder', [MensajeContactoController::class, 'respond'])
        ->middleware('permiso:contacto.editar');
    Route::delete('/mensajes-contacto/{id}', [MensajeContactoController::class, 'destroy'])
        ->middleware('permiso:contacto.eliminar');

    Route::get('/ajustes', [AjusteController::class, 'index'])
        ->middleware('permiso:configuracion.ver');
    Route::get('/ajustes/{key}', [AjusteController::class, 'show'])
        ->middleware('permiso:configuracion.ver');
    Route::put('/ajustes/{key}', [AjusteController::class, 'update'])
        ->middleware('permiso:configuracion.editar');

    Route::get('/configuracion-sitio', [\App\Http\Controllers\Api\ConfiguracionSitioController::class, 'index'])
        ->middleware('permiso:configuracion.ver');
    Route::put('/configuracion-sitio', [\App\Http\Controllers\Api\ConfiguracionSitioController::class, 'update'])
        ->middleware('permiso:configuracion.editar');

    Route::get('/banners-portal', [BannerPortalController::class, 'index'])
        ->middleware('permiso:web.ver');
    Route::post('/banners-portal', [BannerPortalController::class, 'store'])
        ->middleware('permiso:configuracion.editar');
    Route::get('/banners-portal/slug/{slug}', [BannerPortalController::class, 'showBySlug'])
        ->middleware('permiso:web.ver');
    Route::get('/banners-portal/{id}', [BannerPortalController::class, 'show'])
        ->middleware('permiso:web.ver');
    Route::put('/banners-portal/{id}', [BannerPortalController::class, 'update'])
        ->middleware('permiso:configuracion.editar');
    Route::delete('/banners-portal/{id}', [BannerPortalController::class, 'destroy'])
        ->middleware('permiso:configuracion.eliminar');

    Route::get('/asesores', [\App\Http\Controllers\Api\AsesorController::class, 'index'])
        ->middleware('permiso:whatsapp.ver');
    Route::post('/asesores', [\App\Http\Controllers\Api\AsesorController::class, 'store'])
        ->middleware('permiso:whatsapp.crear');
    Route::get('/asesores/{id}', [\App\Http\Controllers\Api\AsesorController::class, 'show'])
        ->middleware('permiso:whatsapp.ver');
    Route::put('/asesores/{id}', [\App\Http\Controllers\Api\AsesorController::class, 'update'])
        ->middleware('permiso:whatsapp.editar');
    Route::delete('/asesores/{id}', [\App\Http\Controllers\Api\AsesorController::class, 'destroy'])
        ->middleware('permiso:whatsapp.eliminar');
    Route::post('/whatsapp/conversaciones/{id}/asesor', [\App\Http\Controllers\Api\AsesorController::class, 'asignar'])
        ->middleware('permiso:whatsapp.crear');

    Route::get('/whatsapp/bot/process-status', [\App\Http\Controllers\Api\WhatsAppBotProcessController::class, 'processStatus'])
        ->middleware('permiso:whatsapp.ver');
    Route::post('/whatsapp/bot/start', [\App\Http\Controllers\Api\WhatsAppBotProcessController::class, 'start'])
        ->middleware('permiso:whatsapp.editar');
    Route::post('/whatsapp/bot/stop', [\App\Http\Controllers\Api\WhatsAppBotProcessController::class, 'stop'])
        ->middleware('permiso:whatsapp.editar');
    Route::get('/whatsapp/bot/logs', [\App\Http\Controllers\Api\WhatsAppBotProcessController::class, 'logs'])
        ->middleware('permiso:whatsapp.ver');
    Route::post('/whatsapp/bot/flush-cache', [\App\Http\Controllers\Api\WhatsAppBotProcessController::class, 'flushCache'])
        ->middleware('permiso:whatsapp.editar');
    Route::post('/whatsapp/bot/connect-active', [\App\Http\Controllers\Api\WhatsAppBotProcessController::class, 'connectActive'])
        ->middleware('permiso:whatsapp.editar');
    Route::post('/whatsapp/bot/disconnect', [\App\Http\Controllers\Api\WhatsAppBotProcessController::class, 'disconnect'])
        ->middleware('permiso:whatsapp.editar');

    Route::get('/whatsapp/conversaciones', [WhatsAppAdminController::class, 'conversaciones'])
        ->middleware('permiso:whatsapp.ver');
    Route::get('/whatsapp/conversaciones/{id}/mensajes', [WhatsAppAdminController::class, 'mensajes'])
        ->middleware('permiso:whatsapp.ver');
    Route::post('/whatsapp/conversaciones/{id}/etiquetas', [WhatsAppAdminController::class, 'asignarEtiquetas'])
        ->middleware('permiso:whatsapp.editar');
    Route::get('/whatsapp/phones', [WhatsAppAdminController::class, 'todosPhones'])
        ->middleware('permiso:whatsapp.ver');
    Route::post('/whatsapp/conversaciones/{id}/atendido', [WhatsAppAdminController::class, 'marcarAtendido'])
        ->middleware('permiso:whatsapp.editar');
    Route::post('/whatsapp/enviar', [WhatsAppAdminController::class, 'enviar'])
        ->middleware('permiso:whatsapp.editar');
    Route::post('/whatsapp/enviar-masivo', [WhatsAppAdminController::class, 'enviarMasivo'])
        ->middleware('permiso:whatsapp.editar');
    Route::post('/whatsapp/enviar-media', [WhatsAppAdminController::class, 'enviarMedia'])
        ->middleware('permiso:whatsapp.editar');
    Route::post('/whatsapp/plantilla', [WhatsAppAdminController::class, 'enviarPlantilla'])
        ->middleware('permiso:whatsapp.editar');
    Route::get('/whatsapp/etiquetas', [WhatsAppAdminController::class, 'etiquetas'])
        ->middleware('permiso:whatsapp.ver');
    Route::post('/whatsapp/etiquetas', [WhatsAppAdminController::class, 'crearEtiqueta'])
        ->middleware('permiso:whatsapp.editar');
    Route::put('/whatsapp/etiquetas/{id}', [WhatsAppAdminController::class, 'actualizarEtiqueta'])
        ->middleware('permiso:whatsapp.editar');
    Route::delete('/whatsapp/etiquetas/{id}', [WhatsAppAdminController::class, 'eliminarEtiqueta'])
        ->middleware('permiso:whatsapp.eliminar');

    Route::get('/cursos-migrados/stats', [\App\Http\Controllers\Api\CursoMigradoController::class, 'stats'])
        ->middleware('permiso:cursos_migrados.ver');
    Route::get('/cursos-migrados/participantes/buscar', [\App\Http\Controllers\Api\CursoMigradoController::class, 'buscarParticipantes'])
        ->middleware('permiso:cursos_migrados.ver');
    Route::post('/cursos-migrados/importar-json', [\App\Http\Controllers\Api\CursoMigradoController::class, 'importarJson'])
        ->middleware('permiso:cursos_migrados.editar');
    Route::get('/cursos-migrados', [\App\Http\Controllers\Api\CursoMigradoController::class, 'index'])
        ->middleware('permiso:cursos_migrados.ver');
    Route::post('/cursos-migrados', [\App\Http\Controllers\Api\CursoMigradoController::class, 'store'])
        ->middleware('permiso:cursos_migrados.crear');
    Route::get('/cursos-migrados/{id}', [\App\Http\Controllers\Api\CursoMigradoController::class, 'show'])
        ->middleware('permiso:cursos_migrados.ver');
    Route::put('/cursos-migrados/{id}', [\App\Http\Controllers\Api\CursoMigradoController::class, 'update'])
        ->middleware('permiso:cursos_migrados.editar');
    Route::delete('/cursos-migrados/{id}', [\App\Http\Controllers\Api\CursoMigradoController::class, 'destroy'])
        ->middleware('permiso:cursos_migrados.eliminar');
    Route::post('/cursos-migrados/{id}/imagen', [\App\Http\Controllers\Api\CursoMigradoController::class, 'uploadImagen'])
        ->middleware('permiso:cursos_migrados.editar');
    Route::delete('/cursos-migrados/{id}/imagen', [\App\Http\Controllers\Api\CursoMigradoController::class, 'deleteImagen'])
        ->middleware('permiso:cursos_migrados.eliminar');
    Route::post('/cursos-migrados/{id}/logos', [\App\Http\Controllers\Api\CursoMigradoController::class, 'uploadLogo'])
        ->middleware('permiso:cursos_migrados.editar');
    Route::delete('/cursos-migrados/{id}/logos/{logoId}', [\App\Http\Controllers\Api\CursoMigradoController::class, 'deleteLogo'])
        ->middleware('permiso:cursos_migrados.eliminar');
    Route::post('/cursos-migrados/{id}/generar-qr', [\App\Http\Controllers\Api\CursoMigradoController::class, 'generarQr'])
        ->middleware('permiso:cursos_migrados.editar');
    Route::post('/cursos-migrados/{id}/participantes', [\App\Http\Controllers\Api\CursoMigradoController::class, 'addParticipante'])
        ->middleware('permiso:cursos_migrados.crear');
    Route::post('/cursos-migrados/{id}/participantes/importar-excel', [\App\Http\Controllers\Api\CursoMigradoController::class, 'importarExcel'])
        ->middleware('permiso:cursos_migrados.editar');
    Route::put('/cursos-migrados/{id}/participantes/{participanteId}', [\App\Http\Controllers\Api\CursoMigradoController::class, 'updateParticipante'])
        ->middleware('permiso:cursos_migrados.editar');
    Route::delete('/cursos-migrados/{id}/participantes/{participanteId}', [\App\Http\Controllers\Api\CursoMigradoController::class, 'deleteParticipante'])
        ->middleware('permiso:cursos_migrados.eliminar');
    Route::get('/cursos-migrados/{id}/export-pdf', [\App\Http\Controllers\Api\CursoMigradoController::class, 'exportPdf'])
        ->middleware('permiso:cursos_migrados.ver');
    Route::get('/cursos-migrados/{id}/export-excel', [\App\Http\Controllers\Api\CursoMigradoController::class, 'exportExcel'])
        ->middleware('permiso:cursos_migrados.ver');
    Route::get('/cursos-migrados-export/pdf', [\App\Http\Controllers\Api\CursoMigradoController::class, 'exportAllPdf'])
        ->middleware('permiso:cursos_migrados.ver');
    Route::get('/cursos-migrados-export/excel', [\App\Http\Controllers\Api\CursoMigradoController::class, 'exportAllExcel'])
        ->middleware('permiso:cursos_migrados.ver');

    Route::get('/areas', [\App\Http\Controllers\Api\AreaController::class, 'index'])
        ->middleware('permiso:programas.ver');
    Route::post('/areas', [\App\Http\Controllers\Api\AreaController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/areas/slug/{slug}', [\App\Http\Controllers\Api\AreaController::class, 'showBySlug'])
        ->middleware('permiso:programas.ver');
    Route::get('/areas/{id}', [\App\Http\Controllers\Api\AreaController::class, 'show'])
        ->middleware('permiso:programas.ver');
    Route::put('/areas/{id}', [\App\Http\Controllers\Api\AreaController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/areas/{id}', [\App\Http\Controllers\Api\AreaController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    // Control Académico
    Route::get('/dashboard/control-academico', [\App\Http\Controllers\Api\DashboardController::class, 'controlAcademico'])
        ->middleware('permiso:programas.ver');
    Route::get('/cursos/{programaId}/tareas', [\App\Http\Controllers\Api\TareaAcademicaController::class, 'index'])
        ->middleware('permiso:programas.ver');
    Route::post('/cursos/{programaId}/tareas', [\App\Http\Controllers\Api\TareaAcademicaController::class, 'store'])
        ->middleware('permiso:programas.editar');
    Route::post('/tareas/{id}/completar', [\App\Http\Controllers\Api\TareaAcademicaController::class, 'completar'])
        ->middleware('permiso:programas.editar');
        
    // Catálogo de Tareas
    Route::get('/catalogo-tareas', [\App\Http\Controllers\Api\CatalogoTareaController::class, 'index'])
        ->middleware('permiso:programas.ver');
    Route::post('/catalogo-tareas', [\App\Http\Controllers\Api\CatalogoTareaController::class, 'store'])
        ->middleware('permiso:programas.editar');
    Route::put('/catalogo-tareas/{id}', [\App\Http\Controllers\Api\CatalogoTareaController::class, 'update'])
        ->middleware('permiso:programas.editar');
    Route::delete('/catalogo-tareas/{id}', [\App\Http\Controllers\Api\CatalogoTareaController::class, 'destroy'])
        ->middleware('permiso:programas.editar');

    Route::get('/cursos', [\App\Http\Controllers\Api\CursoController::class, 'index'])
        ->middleware('permiso:programas.ver');
    Route::post('/cursos', [\App\Http\Controllers\Api\CursoController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/cursos/slug/{slug}', [\App\Http\Controllers\Api\CursoController::class, 'showBySlug'])
        ->middleware('permiso:programas.ver');
    Route::get('/cursos/estadisticas', [\App\Http\Controllers\Api\CursoController::class, 'estadisticas'])
        ->middleware('permiso:programas.ver');
    Route::get('/cursos/estadisticas/detalle', [\App\Http\Controllers\Api\CursoController::class, 'estadisticasDetalle'])
        ->middleware('permiso:programas.ver');
    Route::get('/cursos/reporte-envios-documentos', [\App\Http\Controllers\Api\CursoController::class, 'reporteEnviosDocumentos'])
        ->middleware('permiso:programas.ver');
    Route::get('/cursos/alertas-cobros', [\App\Http\Controllers\Api\CursoController::class, 'alertasCobros'])
        ->middleware('permiso:pagos.ver');
    Route::get('/cursos/moodle-export', [\App\Http\Controllers\Api\CursoController::class, 'moodleExport'])
        ->middleware('permiso:programas.ver');
    Route::get('/cursos/{id}', [\App\Http\Controllers\Api\CursoController::class, 'show'])
        ->middleware('permiso:programas.ver');
    Route::put('/cursos/{id}', [\App\Http\Controllers\Api\CursoController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/cursos/{id}', [\App\Http\Controllers\Api\CursoController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');
    Route::get('/cursos/{id}/docentes', [\App\Http\Controllers\Api\CursoController::class, 'docentesIndex'])
        ->middleware('permiso:programas.ver');
    Route::post('/cursos/{id}/docentes', [\App\Http\Controllers\Api\CursoController::class, 'docentesAttach'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/cursos/{id}/docentes/{docenteId}', [\App\Http\Controllers\Api\CursoController::class, 'docentesDetach'])
        ->middleware('permiso:contenido.editar');
    Route::post('/cursos/{id}/plan-cobros', [\App\Http\Controllers\Api\CursoController::class, 'planCobros'])
        ->middleware('permiso:pagos.crear');

    
    Route::get('/formularios', [\App\Http\Controllers\Api\FormularioController::class, 'index'])
        ->middleware('permiso:configuracion.ver');
    Route::get('/formularios/activos', [\App\Http\Controllers\Api\FormularioController::class, 'activos'])
        ->middleware('permiso:configuracion.ver');
    Route::get('/formularios/{formulario}', [\App\Http\Controllers\Api\FormularioController::class, 'show'])
        ->middleware('permiso:configuracion.ver');
    Route::post('/formularios',         [\App\Http\Controllers\Api\FormularioController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::put('/formularios/{formulario}', [\App\Http\Controllers\Api\FormularioController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/formularios/{formulario}', [\App\Http\Controllers\Api\FormularioController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/categorias-programa', [\App\Http\Controllers\Api\CategoriaProgramaController::class, 'index'])
        ->middleware('permiso:programas.ver');
    Route::post('/categorias-programa', [\App\Http\Controllers\Api\CategoriaProgramaController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/categorias-programa/{id}', [\App\Http\Controllers\Api\CategoriaProgramaController::class, 'show'])
        ->middleware('permiso:programas.ver');
    Route::put('/categorias-programa/{id}', [\App\Http\Controllers\Api\CategoriaProgramaController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/categorias-programa/{id}', [\App\Http\Controllers\Api\CategoriaProgramaController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/categorias-programa/{categoriaId}/campos', [\App\Http\Controllers\Api\CategoriaCampoController::class, 'index'])
        ->middleware('permiso:programas.ver');
    Route::post('/categorias-programa/{categoriaId}/campos', [\App\Http\Controllers\Api\CategoriaCampoController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::put('/categorias-programa/{categoriaId}/campos/{id}', [\App\Http\Controllers\Api\CategoriaCampoController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/categorias-programa/{categoriaId}/campos/{id}', [\App\Http\Controllers\Api\CategoriaCampoController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');
    Route::post('/categorias-programa/{categoriaId}/campos/reorder', [\App\Http\Controllers\Api\CategoriaCampoController::class, 'reorder'])
        ->middleware('permiso:contenido.editar');
    Route::post('/categorias-programa/{categoriaId}/campos/seed', [\App\Http\Controllers\Api\CategoriaCampoController::class, 'seedDefaults'])
        ->middleware('permiso:contenido.editar');

    Route::get('/tipos-programa', [\App\Http\Controllers\Api\CategoriaProgramaController::class, 'indexComoTipos'])
        ->middleware('permiso:programas.ver');
    Route::get('/tipos-programa/legacy', [\App\Http\Controllers\Api\CategoriaProgramaController::class, 'tiposLegacy'])
        ->middleware('permiso:programas.ver');


    Route::get('/inscripciones/{id}/documentos', [\App\Http\Controllers\Api\InscripcionController::class, 'documentos'])
        ->middleware('permiso:inscripciones.ver');
    Route::post('/inscripciones/{id}/documentos', [\App\Http\Controllers\Api\InscripcionController::class, 'registrarDocumento'])
        ->middleware('permiso:inscripciones.editar');
    Route::post('/inscripciones/{id}/anticipo', [\App\Http\Controllers\Api\InscripcionController::class, 'registrarAnticipo'])
        ->middleware('permiso:inscripciones.editar');
    Route::post('/inscripciones/{id}/marcar-participante', [\App\Http\Controllers\Api\InscripcionController::class, 'marcarParticipante'])
        ->middleware('permiso:inscripciones.editar');
    Route::post('/inscripciones/marcar-participantes', [\App\Http\Controllers\Api\InscripcionController::class, 'marcarParticipantesBulk'])
        ->middleware('permiso:inscripciones.editar');
    Route::get('/inscripciones/{id}/devoluciones', [\App\Http\Controllers\Api\DevolucionController::class, 'index'])
        ->middleware('permiso:inscripciones.ver');
    Route::post('/inscripciones/{id}/devoluciones', [\App\Http\Controllers\Api\DevolucionController::class, 'store'])
        ->middleware('permiso:inscripciones.editar');
    Route::patch('/devoluciones/{id}/cancelar', [\App\Http\Controllers\Api\DevolucionController::class, 'update'])
        ->middleware('permiso:inscripciones.editar');
    Route::patch('/devoluciones/{id}/resolver', [\App\Http\Controllers\Api\DevolucionController::class, 'update'])
        ->middleware('permiso:devoluciones.aprobar');


    Route::get('/inscripciones-diplomado', [\App\Http\Controllers\Api\InscripcionDiplomadoController::class, 'index'])
        ->middleware('permiso:diplomados.ver');
    Route::post('/inscripciones-diplomado', [\App\Http\Controllers\Api\InscripcionDiplomadoController::class, 'store'])
        ->middleware('permiso:diplomados.crear');
    Route::get('/inscripciones-diplomado/{id}', [\App\Http\Controllers\Api\InscripcionDiplomadoController::class, 'show'])
        ->middleware('permiso:diplomados.ver');
    Route::put('/inscripciones-diplomado/{id}', [\App\Http\Controllers\Api\InscripcionDiplomadoController::class, 'update'])
        ->middleware('permiso:diplomados.editar');
    Route::post('/inscripciones-diplomado/{id}/convertir-inscripcion', [\App\Http\Controllers\Api\InscripcionDiplomadoController::class, 'convertirInscripcion'])
        ->middleware('permiso:diplomados.editar');
    Route::delete('/inscripciones-diplomado/{id}', [\App\Http\Controllers\Api\InscripcionDiplomadoController::class, 'destroy'])
        ->middleware('permiso:diplomados.eliminar');

    Route::get('/resenas', [\App\Http\Controllers\Api\ResenaController::class, 'index'])
        ->middleware('permiso:contenido.ver');
    Route::post('/resenas', [\App\Http\Controllers\Api\ResenaController::class, 'store'])
        ->middleware('permiso:contenido.editar');
    Route::get('/resenas/{id}', [\App\Http\Controllers\Api\ResenaController::class, 'show'])
        ->middleware('permiso:contenido.ver');
    Route::put('/resenas/{id}', [\App\Http\Controllers\Api\ResenaController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/resenas/{id}', [\App\Http\Controllers\Api\ResenaController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');
    Route::get('/resenas-programa/{programaId}/estudiantes', [\App\Http\Controllers\Api\ResenaController::class, 'estudiantesPrograma'])
        ->middleware('permiso:contenido.ver');

    Route::get('/faqs', [\App\Http\Controllers\Api\FaqController::class, 'index'])
        ->middleware('permiso:contenido.ver');
    Route::post('/faqs', [\App\Http\Controllers\Api\FaqController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/faqs/{id}', [\App\Http\Controllers\Api\FaqController::class, 'show'])
        ->middleware('permiso:contenido.ver');
    Route::put('/faqs/{id}', [\App\Http\Controllers\Api\FaqController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/faqs/{id}', [\App\Http\Controllers\Api\FaqController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/cifras-institucionales', [\App\Http\Controllers\Api\CifraInstitucionalController::class, 'index'])
        ->middleware('permiso:contenido.ver');
    Route::post('/cifras-institucionales', [\App\Http\Controllers\Api\CifraInstitucionalController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/cifras-institucionales/{id}', [\App\Http\Controllers\Api\CifraInstitucionalController::class, 'show'])
        ->middleware('permiso:contenido.ver');
    Route::put('/cifras-institucionales/{id}', [\App\Http\Controllers\Api\CifraInstitucionalController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/cifras-institucionales/{id}', [\App\Http\Controllers\Api\CifraInstitucionalController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/hitos-institucionales', [\App\Http\Controllers\Api\HitoInstitucionalController::class, 'index'])
        ->middleware('permiso:contenido.ver');
    Route::post('/hitos-institucionales', [\App\Http\Controllers\Api\HitoInstitucionalController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/hitos-institucionales/{id}', [\App\Http\Controllers\Api\HitoInstitucionalController::class, 'show'])
        ->middleware('permiso:contenido.ver');
    Route::put('/hitos-institucionales/{id}', [\App\Http\Controllers\Api\HitoInstitucionalController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/hitos-institucionales/{id}', [\App\Http\Controllers\Api\HitoInstitucionalController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/expedido', [\App\Http\Controllers\Api\ExpedidoController::class, 'index'])
        ->middleware('permiso:estudiantes.ver');
    Route::post('/expedido', [\App\Http\Controllers\Api\ExpedidoController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/expedido/{id}', [\App\Http\Controllers\Api\ExpedidoController::class, 'show'])
        ->middleware('permiso:estudiantes.ver');
    Route::put('/expedido/{id}', [\App\Http\Controllers\Api\ExpedidoController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/expedido/{id}', [\App\Http\Controllers\Api\ExpedidoController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/grados-academicos', [\App\Http\Controllers\Api\GradoAcademicoController::class, 'index'])
        ->middleware('permiso:estudiantes.ver');
    Route::post('/grados-academicos', [\App\Http\Controllers\Api\GradoAcademicoController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/grados-academicos/{id}', [\App\Http\Controllers\Api\GradoAcademicoController::class, 'show'])
        ->middleware('permiso:estudiantes.ver');
    Route::put('/grados-academicos/{id}', [\App\Http\Controllers\Api\GradoAcademicoController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/grados-academicos/{id}', [\App\Http\Controllers\Api\GradoAcademicoController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/profesiones', [\App\Http\Controllers\Api\ProfesionController::class, 'index'])
        ->middleware('permiso:estudiantes.ver');
    Route::post('/profesiones', [\App\Http\Controllers\Api\ProfesionController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/profesiones/{id}', [\App\Http\Controllers\Api\ProfesionController::class, 'show'])
        ->middleware('permiso:estudiantes.ver');
    Route::put('/profesiones/{id}', [\App\Http\Controllers\Api\ProfesionController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/profesiones/{id}', [\App\Http\Controllers\Api\ProfesionController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/medios-pago', [\App\Http\Controllers\Api\MedioPagoController::class, 'index'])
        ->middleware('permiso:pagos.ver');
    Route::post('/medios-pago', [\App\Http\Controllers\Api\MedioPagoController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/medios-pago/{id}', [\App\Http\Controllers\Api\MedioPagoController::class, 'show'])
        ->middleware('permiso:pagos.ver');
    Route::put('/medios-pago/{id}', [\App\Http\Controllers\Api\MedioPagoController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/medios-pago/{id}', [\App\Http\Controllers\Api\MedioPagoController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/web-menus', [\App\Http\Controllers\Api\WebMenuController::class, 'index'])
        ->middleware('permiso:contenido.ver');
    Route::post('/web-menus', [\App\Http\Controllers\Api\WebMenuController::class, 'store'])
        ->middleware('permiso:contenido.editar');
    Route::get('/web-menus/{id}', [\App\Http\Controllers\Api\WebMenuController::class, 'show'])
        ->middleware('permiso:contenido.ver');
    Route::put('/web-menus/{id}', [\App\Http\Controllers\Api\WebMenuController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/web-menus/{id}', [\App\Http\Controllers\Api\WebMenuController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/web-menu-items', [\App\Http\Controllers\Api\WebMenuItemController::class, 'index'])
        ->middleware('permiso:contenido.ver');
    Route::post('/web-menu-items', [\App\Http\Controllers\Api\WebMenuItemController::class, 'store'])
        ->middleware('permiso:contenido.editar');
    Route::get('/web-menu-items/{id}', [\App\Http\Controllers\Api\WebMenuItemController::class, 'show'])
        ->middleware('permiso:contenido.ver');
    Route::put('/web-menu-items/{id}', [\App\Http\Controllers\Api\WebMenuItemController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/web-menu-items/{id}', [\App\Http\Controllers\Api\WebMenuItemController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/config-sitio', [\App\Http\Controllers\Api\ConfigSitioController::class, 'show'])
        ->middleware('permiso:configuracion.ver');
    Route::put('/config-sitio', [\App\Http\Controllers\Api\ConfigSitioController::class, 'update'])
        ->middleware('permiso:transparencia.crear');

    Route::get('/permisos', [\App\Http\Controllers\Api\PermisoController::class, 'index'])
        ->middleware('permiso:usuarios.ver');
    Route::post('/permisos', [\App\Http\Controllers\Api\PermisoController::class, 'store'])
        ->middleware('permiso:usuarios.crear');
    Route::get('/permisos/{id}', [\App\Http\Controllers\Api\PermisoController::class, 'show'])
        ->middleware('permiso:usuarios.ver');
    Route::put('/permisos/{id}', [\App\Http\Controllers\Api\PermisoController::class, 'update'])
        ->middleware('permiso:usuarios.editar');
    Route::delete('/permisos/{id}', [\App\Http\Controllers\Api\PermisoController::class, 'destroy'])
        ->middleware('permiso:usuarios.eliminar');

    Route::get('/settings', [\App\Http\Controllers\Api\SettingsController::class, 'index'])
        ->middleware('permiso:configuracion.ver');
    Route::put('/settings', [\App\Http\Controllers\Api\SettingsController::class, 'update'])
        ->middleware('permiso:configuracion.editar');

    Route::get('/settings/cobro-estado', [\App\Http\Controllers\Api\CobroEstadoSettingsController::class, 'index'])
        ->middleware('permiso:inscripciones.ver');
    Route::put('/settings/cobro-estado', [\App\Http\Controllers\Api\CobroEstadoSettingsController::class, 'update'])
        ->middleware('permiso:configuracion.editar');

    Route::get('/suscriptores', [\App\Http\Controllers\Api\SuscriptorController::class, 'index'])
        ->middleware('permiso:contenido.ver');
    Route::post('/suscriptores', [\App\Http\Controllers\Api\SuscriptorController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/suscriptores/{id}', [\App\Http\Controllers\Api\SuscriptorController::class, 'show'])
        ->middleware('permiso:contenido.ver');
    Route::put('/suscriptores/{id}', [\App\Http\Controllers\Api\SuscriptorController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/suscriptores/{id}', [\App\Http\Controllers\Api\SuscriptorController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/testimonios', [\App\Http\Controllers\Api\TestimonioController::class, 'index'])
        ->middleware('permiso:contenido.ver');
    Route::post('/testimonios', [\App\Http\Controllers\Api\TestimonioController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/testimonios/slug/{slug}', [\App\Http\Controllers\Api\TestimonioController::class, 'showBySlug'])
        ->middleware('permiso:contenido.ver');
    Route::get('/testimonios/{id}', [\App\Http\Controllers\Api\TestimonioController::class, 'show'])
        ->middleware('permiso:contenido.ver');
    Route::put('/testimonios/{id}', [\App\Http\Controllers\Api\TestimonioController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/testimonios/{id}', [\App\Http\Controllers\Api\TestimonioController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/aliados', [\App\Http\Controllers\Api\AliadoController::class, 'index'])
        ->middleware('permiso:contenido.ver');
    Route::post('/aliados', [\App\Http\Controllers\Api\AliadoController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/aliados/{id}', [\App\Http\Controllers\Api\AliadoController::class, 'show'])
        ->middleware('permiso:contenido.ver');
    Route::put('/aliados/{id}', [\App\Http\Controllers\Api\AliadoController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/aliados/{id}', [\App\Http\Controllers\Api\AliadoController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/docentes-perfil', [\App\Http\Controllers\Api\DocentePerfilController::class, 'index'])
        ->middleware('permiso:docentes.ver');
    Route::get('/docentes-perfil/reporte/materias', [\App\Http\Controllers\Api\DocentePerfilController::class, 'reporteMaterias'])
        ->middleware('permiso:docentes.ver');
    Route::post('/docentes-perfil', [\App\Http\Controllers\Api\DocentePerfilController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/docentes-perfil/slug/{slug}', [\App\Http\Controllers\Api\DocentePerfilController::class, 'showBySlug'])
        ->middleware('permiso:docentes.ver');
    Route::get('/docentes-perfil/{id}', [\App\Http\Controllers\Api\DocentePerfilController::class, 'show'])
        ->middleware('permiso:docentes.ver');
    Route::put('/docentes-perfil/{id}', [\App\Http\Controllers\Api\DocentePerfilController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/docentes-perfil/{id}', [\App\Http\Controllers\Api\DocentePerfilController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/popups', [\App\Http\Controllers\Api\PopupController::class, 'index'])
        ->middleware('permiso:web.ver');
    Route::post('/popups', [\App\Http\Controllers\Api\PopupController::class, 'store'])
        ->middleware('permiso:configuracion.editar');
    Route::get('/popups/{id}', [\App\Http\Controllers\Api\PopupController::class, 'show'])
        ->middleware('permiso:web.ver');
    Route::put('/popups/{id}', [\App\Http\Controllers\Api\PopupController::class, 'update'])
        ->middleware('permiso:configuracion.editar');
    Route::delete('/popups/{id}', [\App\Http\Controllers\Api\PopupController::class, 'destroy'])
        ->middleware('permiso:configuracion.eliminar');

    Route::get('/whatsapp-grupos', [\App\Http\Controllers\Api\WhatsappGrupoController::class, 'index'])
        ->middleware('permiso:whatsapp.ver');
    Route::post('/whatsapp-grupos', [\App\Http\Controllers\Api\WhatsappGrupoController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/whatsapp-grupos/{id}', [\App\Http\Controllers\Api\WhatsappGrupoController::class, 'show'])
        ->middleware('permiso:whatsapp.ver');
    Route::put('/whatsapp-grupos/{id}', [\App\Http\Controllers\Api\WhatsappGrupoController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/whatsapp-grupos/{id}', [\App\Http\Controllers\Api\WhatsappGrupoController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/galeria-categorias', [\App\Http\Controllers\Api\GaleriaCategoriaController::class, 'index'])
        ->middleware('permiso:contenido.ver');
    Route::post('/galeria-categorias', [\App\Http\Controllers\Api\GaleriaCategoriaController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/galeria-categorias/{id}', [\App\Http\Controllers\Api\GaleriaCategoriaController::class, 'show'])
        ->middleware('permiso:contenido.ver');
    Route::put('/galeria-categorias/{id}', [\App\Http\Controllers\Api\GaleriaCategoriaController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/galeria-categorias/{id}', [\App\Http\Controllers\Api\GaleriaCategoriaController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/acreditaciones', [\App\Http\Controllers\Api\AcreditacionController::class, 'index'])
        ->middleware('permiso:contenido.ver');
    Route::post('/acreditaciones', [\App\Http\Controllers\Api\AcreditacionController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/acreditaciones/{id}', [\App\Http\Controllers\Api\AcreditacionController::class, 'show'])
        ->middleware('permiso:contenido.ver');
    Route::put('/acreditaciones/{id}', [\App\Http\Controllers\Api\AcreditacionController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/acreditaciones/{id}', [\App\Http\Controllers\Api\AcreditacionController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/notas-prensa', [\App\Http\Controllers\Api\NotaPrensaController::class, 'index'])
        ->middleware('permiso:noticias.ver');
    Route::post('/notas-prensa', [\App\Http\Controllers\Api\NotaPrensaController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/notas-prensa/{id}', [\App\Http\Controllers\Api\NotaPrensaController::class, 'show'])
        ->middleware('permiso:noticias.ver');
    Route::put('/notas-prensa/{id}', [\App\Http\Controllers\Api\NotaPrensaController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/notas-prensa/{id}', [\App\Http\Controllers\Api\NotaPrensaController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/descargables', [\App\Http\Controllers\Api\DescargableController::class, 'index'])
        ->middleware('permiso:contenido.ver');
    Route::post('/descargables', [\App\Http\Controllers\Api\DescargableController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/descargables/{id}', [\App\Http\Controllers\Api\DescargableController::class, 'show'])
        ->middleware('permiso:contenido.ver');
    Route::put('/descargables/{id}', [\App\Http\Controllers\Api\DescargableController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/descargables/{id}', [\App\Http\Controllers\Api\DescargableController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/galeria-videos', [\App\Http\Controllers\Api\GaleriaVideoController::class, 'index'])
        ->middleware('permiso:contenido.ver');
    Route::post('/galeria-videos', [\App\Http\Controllers\Api\GaleriaVideoController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/galeria-videos/{id}', [\App\Http\Controllers\Api\GaleriaVideoController::class, 'show'])
        ->middleware('permiso:contenido.ver');
    Route::put('/galeria-videos/{id}', [\App\Http\Controllers\Api\GaleriaVideoController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/galeria-videos/{id}', [\App\Http\Controllers\Api\GaleriaVideoController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/calendario-academico', [\App\Http\Controllers\Api\CalendarioAcademicoController::class, 'index'])
        ->middleware('permiso:programas.ver');
    Route::get('/calendario-academico/cursos-vigentes', [\App\Http\Controllers\Api\CalendarioAcademicoController::class, 'vigentes'])
        ->middleware('permiso:programas.ver');
    Route::post('/calendario-academico', [\App\Http\Controllers\Api\CalendarioAcademicoController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/calendario-academico/{id}', [\App\Http\Controllers\Api\CalendarioAcademicoController::class, 'show'])
        ->middleware('permiso:programas.ver');
    Route::put('/calendario-academico/{id}', [\App\Http\Controllers\Api\CalendarioAcademicoController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/calendario-academico/{id}', [\App\Http\Controllers\Api\CalendarioAcademicoController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');
    Route::get('/calendario-academico-export/pdf', [\App\Http\Controllers\Api\CalendarioAcademicoController::class, 'exportPdf'])
        ->middleware('permiso:programas.ver');
    Route::get('/calendario-academico-export/excel', [\App\Http\Controllers\Api\CalendarioAcademicoController::class, 'exportExcel'])
        ->middleware('permiso:programas.ver');

    Route::get('/redirecciones', [\App\Http\Controllers\Api\RedireccionController::class, 'index'])
        ->middleware('permiso:configuracion.editar');
    Route::post('/redirecciones', [\App\Http\Controllers\Api\RedireccionController::class, 'store'])
        ->middleware('permiso:configuracion.editar');
    Route::get('/redirecciones/{id}', [\App\Http\Controllers\Api\RedireccionController::class, 'show'])
        ->middleware('permiso:configuracion.editar');
    Route::put('/redirecciones/{id}', [\App\Http\Controllers\Api\RedireccionController::class, 'update'])
        ->middleware('permiso:configuracion.editar');
    Route::delete('/redirecciones/{id}', [\App\Http\Controllers\Api\RedireccionController::class, 'destroy'])
        ->middleware('permiso:configuracion.eliminar');

    Route::get('/descuentos-promociones', [\App\Http\Controllers\Api\DescuentoPromocionController::class, 'index'])
        ->middleware('permiso:pagos.ver');
    Route::post('/descuentos-promociones', [\App\Http\Controllers\Api\DescuentoPromocionController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/descuentos-promociones/{id}', [\App\Http\Controllers\Api\DescuentoPromocionController::class, 'show'])
        ->middleware('permiso:pagos.ver');
    Route::put('/descuentos-promociones/{id}', [\App\Http\Controllers\Api\DescuentoPromocionController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/descuentos-promociones/{id}', [\App\Http\Controllers\Api\DescuentoPromocionController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/cert-plantillas', [\App\Http\Controllers\Api\CertPlantillaController::class, 'index'])
        ->middleware('permiso:certificados.ver');
    Route::post('/cert-plantillas', [\App\Http\Controllers\Api\CertPlantillaController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/cert-plantillas/{id}', [\App\Http\Controllers\Api\CertPlantillaController::class, 'show'])
        ->middleware('permiso:certificados.ver');
    Route::get('/cert-plantillas/{id}/preview', [\App\Http\Controllers\Api\CertPlantillaController::class, 'preview'])
        ->middleware('permiso:certificados.ver');
    Route::put('/cert-plantillas/{id}', [\App\Http\Controllers\Api\CertPlantillaController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/cert-plantillas/{id}', [\App\Http\Controllers\Api\CertPlantillaController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/cert-plantilla-campos', [\App\Http\Controllers\Api\CertPlantillaCampoController::class, 'index'])
        ->middleware('permiso:certificados.ver');
    Route::post('/cert-plantilla-campos', [\App\Http\Controllers\Api\CertPlantillaCampoController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/cert-plantilla-campos/{id}', [\App\Http\Controllers\Api\CertPlantillaCampoController::class, 'show'])
        ->middleware('permiso:certificados.ver');
    Route::put('/cert-plantilla-campos/{id}', [\App\Http\Controllers\Api\CertPlantillaCampoController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/cert-plantilla-campos/{id}', [\App\Http\Controllers\Api\CertPlantillaCampoController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/lista-aprobados', [\App\Http\Controllers\Api\ListaAprobadosController::class, 'index'])
        ->middleware('permiso:inscripciones.ver');
    Route::post('/lista-aprobados', [\App\Http\Controllers\Api\ListaAprobadosController::class, 'store'])
        ->middleware('permiso:inscripciones.editar');
    Route::delete('/lista-aprobados/bulk', [\App\Http\Controllers\Api\ListaAprobadosController::class, 'destroyBulk'])
        ->middleware('permiso:inscripciones.eliminar');
    Route::get('/lista-aprobados/{id}', [\App\Http\Controllers\Api\ListaAprobadosController::class, 'show'])
        ->middleware('permiso:inscripciones.ver');
    Route::put('/lista-aprobados/{id}', [\App\Http\Controllers\Api\ListaAprobadosController::class, 'update'])
        ->middleware('permiso:inscripciones.editar');
    Route::delete('/lista-aprobados/{id}', [\App\Http\Controllers\Api\ListaAprobadosController::class, 'destroy'])
        ->middleware('permiso:inscripciones.eliminar');

    Route::post('/participantes/registrar', [\App\Http\Controllers\Api\ParticipanteCursoController::class, 'registrar'])
        ->middleware('permiso:inscripciones.editar');
    Route::post('/participantes/importar-excel', [\App\Http\Controllers\Api\ParticipanteCursoController::class, 'importarExcel'])
        ->middleware('permiso:inscripciones.editar');

    Route::get('/certificados-externos/cursos', [\App\Http\Controllers\Api\CertificadoExternoController::class, 'index'])
        ->middleware('permiso:certificados.ver');
    Route::post('/certificados-externos/cursos/{cursoId}/estudiantes', [\App\Http\Controllers\Api\CertificadoExternoController::class, 'registrarEstudiante'])
        ->middleware('permiso:certificados.crear');

    Route::get('/certificados', [\App\Http\Controllers\Api\CertificadoController::class, 'index'])
        ->middleware('permiso:certificados.ver');
    Route::post('/certificados', [\App\Http\Controllers\Api\CertificadoController::class, 'store'])
        ->middleware('permiso:certificados.crear');
    Route::get('/certificados/preview-pagos-completos', [\App\Http\Controllers\Api\CertificadoController::class, 'previewPagosCompletos'])
        ->middleware('permiso:certificados.crear');
    Route::post('/certificados/generar-pagos-completos', [\App\Http\Controllers\Api\CertificadoController::class, 'generarPagosCompletos'])
        ->middleware('permiso:certificados.crear');
    Route::get('/certificados/descargar-zip', [\App\Http\Controllers\Api\CertificadoController::class, 'descargarZip'])
        ->middleware('permiso:certificados.crear');
    Route::post('/certificados/generar-lote', [\App\Http\Controllers\Api\CertificadoController::class, 'generarLote'])
        ->middleware('permiso:certificados.crear');
    Route::get('/certificados/codigo/{codigo}', [\App\Http\Controllers\Api\CertificadoController::class, 'showByCode'])
        ->middleware('permiso:certificados.ver');
    Route::get('/certificados/{id}', [\App\Http\Controllers\Api\CertificadoController::class, 'show'])
        ->middleware('permiso:certificados.ver');
    Route::put('/certificados/{id}', [\App\Http\Controllers\Api\CertificadoController::class, 'update'])
        ->middleware('permiso:certificados.editar');
    Route::delete('/certificados/{id}', [\App\Http\Controllers\Api\CertificadoController::class, 'destroy'])
        ->middleware('permiso:certificados.eliminar');

    Route::get('/cert-verificaciones', [\App\Http\Controllers\Api\CertVerificacionController::class, 'index'])
        ->middleware('permiso:inscripciones.ver');
    Route::post('/cert-verificaciones', [\App\Http\Controllers\Api\CertVerificacionController::class, 'store'])
        ->middleware('permiso:certificados.crear');
    Route::get('/cert-verificaciones/{id}', [\App\Http\Controllers\Api\CertVerificacionController::class, 'show'])
        ->middleware('permiso:inscripciones.ver');

    Route::get('/carreras', [\App\Http\Controllers\Api\CarreraController::class, 'index'])
        ->middleware('permiso:estudiantes.ver');
    Route::post('/carreras', [\App\Http\Controllers\Api\CarreraController::class, 'store'])
        ->middleware('permiso:estudiantes.crear');
    Route::get('/carreras/{id}', [\App\Http\Controllers\Api\CarreraController::class, 'show'])
        ->middleware('permiso:estudiantes.ver');
    Route::put('/carreras/{id}', [\App\Http\Controllers\Api\CarreraController::class, 'update'])
        ->middleware('permiso:estudiantes.editar');
    Route::delete('/carreras/{id}', [\App\Http\Controllers\Api\CarreraController::class, 'destroy'])
        ->middleware('permiso:estudiantes.eliminar');

    Route::get('/materias', [\App\Http\Controllers\Api\MateriaController::class, 'index'])
        ->middleware('permiso:programas.ver');
    Route::get('/materias/{id}', [\App\Http\Controllers\Api\MateriaController::class, 'show'])
        ->middleware('permiso:programas.ver');

    Route::get('/usuarios-academicos', [\App\Http\Controllers\Api\UsuarioAcademicoController::class, 'index'])
        ->middleware('permiso:estudiantes.ver');
    Route::post('/usuarios-academicos', [\App\Http\Controllers\Api\UsuarioAcademicoController::class, 'store'])
        ->middleware('permiso:estudiantes.crear');
    Route::get('/usuarios-academicos/{id}', [\App\Http\Controllers\Api\UsuarioAcademicoController::class, 'show'])
        ->middleware('permiso:estudiantes.ver');
    Route::put('/usuarios-academicos/{id}', [\App\Http\Controllers\Api\UsuarioAcademicoController::class, 'update'])
        ->middleware('permiso:estudiantes.editar');
    Route::delete('/usuarios-academicos/{id}', [\App\Http\Controllers\Api\UsuarioAcademicoController::class, 'destroy'])
        ->middleware('permiso:estudiantes.eliminar');

    Route::get('/imparticiones', [\App\Http\Controllers\Api\ImparteController::class, 'index'])
        ->middleware('permiso:programas.ver');
    Route::post('/imparticiones', [\App\Http\Controllers\Api\ImparteController::class, 'store'])
        ->middleware('permiso:programas.crear');
    Route::get('/imparticiones/{id}', [\App\Http\Controllers\Api\ImparteController::class, 'show'])
        ->middleware('permiso:programas.ver');
    Route::put('/imparticiones/{id}', [\App\Http\Controllers\Api\ImparteController::class, 'update'])
        ->middleware('permiso:programas.editar');
    Route::delete('/imparticiones/{id}', [\App\Http\Controllers\Api\ImparteController::class, 'destroy'])
        ->middleware('permiso:programas.eliminar');

    Route::get('/inscripciones', [\App\Http\Controllers\Api\InscripcionController::class, 'index'])
        ->middleware('permiso:inscripciones.ver');
    Route::get('/inscripciones/cursos', [\App\Http\Controllers\Api\InscripcionController::class, 'cursos'])
        ->middleware('permiso:inscripciones.ver');
    Route::get('/inscripciones/reportes', [\App\Http\Controllers\Api\InscripcionController::class, 'reportes'])
        ->middleware('permiso:inscripciones.ver');
    Route::post('/inscripciones', [\App\Http\Controllers\Api\InscripcionController::class, 'store'])
        ->middleware('permiso:inscripciones.crear');
    Route::get('/inscripciones/{id}', [\App\Http\Controllers\Api\InscripcionController::class, 'show'])
        ->middleware('permiso:inscripciones.ver');
    Route::put('/inscripciones/{id}', [\App\Http\Controllers\Api\InscripcionController::class, 'update'])
        ->middleware('permiso:inscripciones.editar');
    Route::delete('/inscripciones/{id}', [\App\Http\Controllers\Api\InscripcionController::class, 'destroy'])
        ->middleware('permiso:inscripciones.eliminar');

    Route::get('/directorio-archivos/cursos', [\App\Http\Controllers\Api\DirectorioArchivosController::class, 'cursos'])
        ->middleware('permiso:directorio-archivos.ver');
    Route::get('/directorio-archivos/cursos/{idImp}/participantes', [\App\Http\Controllers\Api\DirectorioArchivosController::class, 'participantes'])
        ->middleware('permiso:directorio-archivos.ver');
    Route::get('/directorio-archivos/participantes/{idIns}/archivos', [\App\Http\Controllers\Api\DirectorioArchivosController::class, 'archivos'])
        ->middleware('permiso:directorio-archivos.ver');

    Route::get('/notas-academicas', [\App\Http\Controllers\Api\NotaController::class, 'index'])
        ->middleware('permiso:notas.ver');
    Route::post('/notas-academicas', [\App\Http\Controllers\Api\NotaController::class, 'store'])
        ->middleware('permiso:notas.crear');
    Route::get('/notas-academicas/{id}', [\App\Http\Controllers\Api\NotaController::class, 'show'])
        ->middleware('permiso:notas.ver');
    Route::put('/notas-academicas/{id}', [\App\Http\Controllers\Api\NotaController::class, 'update'])
        ->middleware('permiso:notas.editar');
    Route::delete('/notas-academicas/{id}', [\App\Http\Controllers\Api\NotaController::class, 'destroy'])
        ->middleware('permiso:notas.eliminar');

    Route::get('/pagos-academicos', [\App\Http\Controllers\Api\PagoController::class, 'index'])
        ->middleware('permiso:pagos.ver');
    Route::post('/pagos-academicos', [\App\Http\Controllers\Api\PagoController::class, 'store'])
        ->middleware('permiso:pagos.crear');
    Route::get('/pagos-academicos/{id}', [\App\Http\Controllers\Api\PagoController::class, 'show'])
        ->middleware('permiso:pagos.ver');
    Route::put('/pagos-academicos/{id}', [\App\Http\Controllers\Api\PagoController::class, 'update'])
        ->middleware('permiso:pagos.editar');
    Route::patch('/pagos-academicos/{id}/verificar', [\App\Http\Controllers\Api\PagoController::class, 'verificar'])
        ->middleware('permiso:pagos.editar');
    Route::patch('/pagos-academicos/{id}/observar', [\App\Http\Controllers\Api\PagoController::class, 'observar'])
        ->middleware('permiso:pagos.editar');
    Route::delete('/pagos-academicos/{id}', [\App\Http\Controllers\Api\PagoController::class, 'destroy'])
        ->middleware('permiso:pagos.eliminar');

    
    Route::get('/tipos-banco',           [\App\Http\Controllers\Api\TipoBancoController::class, 'index'])
        ->middleware('permiso:pagos.ver');
    Route::get('/tipos-banco/activos',   [\App\Http\Controllers\Api\TipoBancoController::class, 'activos'])
        ->middleware('permiso:pagos.ver');
    Route::get('/tipos-banco/{id}',      [\App\Http\Controllers\Api\TipoBancoController::class, 'show'])
        ->middleware('permiso:pagos.ver');
    Route::post('/tipos-banco',          [\App\Http\Controllers\Api\TipoBancoController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::put('/tipos-banco/{id}',      [\App\Http\Controllers\Api\TipoBancoController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/tipos-banco/{id}',   [\App\Http\Controllers\Api\TipoBancoController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    
    Route::get('/envios-certificado',        [\App\Http\Controllers\Api\EnvioCertificadoController::class, 'index'])
        ->middleware('permiso:inscripciones.ver');
    Route::post('/envios-certificado',       [\App\Http\Controllers\Api\EnvioCertificadoController::class, 'store'])
        ->middleware('permiso:inscripciones.editar');
    Route::delete('/envios-certificado/{id}', [\App\Http\Controllers\Api\EnvioCertificadoController::class, 'destroy'])
        ->middleware('permiso:inscripciones.editar');

    Route::get('/horarios-academicos', [\App\Http\Controllers\Api\HorarioController::class, 'index'])
        ->middleware('permiso:horarios.ver');
    Route::post('/horarios-academicos', [\App\Http\Controllers\Api\HorarioController::class, 'store'])
        ->middleware('permiso:horarios.crear');
    Route::get('/horarios-academicos/{id}', [\App\Http\Controllers\Api\HorarioController::class, 'show'])
        ->middleware('permiso:horarios.ver');
    Route::put('/horarios-academicos/{id}', [\App\Http\Controllers\Api\HorarioController::class, 'update'])
        ->middleware('permiso:horarios.editar');
    Route::delete('/horarios-academicos/{id}', [\App\Http\Controllers\Api\HorarioController::class, 'destroy'])
        ->middleware('permiso:horarios.eliminar');

    Route::get('/convenios/all', [\App\Http\Controllers\Api\ConvenioController::class, 'all'])
        ->middleware('permiso:programas.ver');
    Route::get('/convenios', [\App\Http\Controllers\Api\ConvenioController::class, 'index'])
        ->middleware('permiso:programas.ver');
    Route::post('/convenios', [\App\Http\Controllers\Api\ConvenioController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/convenios/{id}', [\App\Http\Controllers\Api\ConvenioController::class, 'show'])
        ->middleware('permiso:programas.ver');
    Route::match(['put','post'], '/convenios/{id}', [\App\Http\Controllers\Api\ConvenioController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/convenios/{id}', [\App\Http\Controllers\Api\ConvenioController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/planes-academicos', [\App\Http\Controllers\Api\PlanAcademicoController::class, 'index'])
        ->middleware('permiso:planes.ver');
    Route::post('/planes-academicos', [\App\Http\Controllers\Api\PlanAcademicoController::class, 'store'])
        ->middleware('permiso:planes.crear');
    Route::get('/planes-academicos/{id}', [\App\Http\Controllers\Api\PlanAcademicoController::class, 'show'])
        ->middleware('permiso:planes.ver');
    Route::put('/planes-academicos/{id}', [\App\Http\Controllers\Api\PlanAcademicoController::class, 'update'])
        ->middleware('permiso:planes.editar');
    Route::post('/planes-academicos/{id}', [\App\Http\Controllers\Api\PlanAcademicoController::class, 'update'])
        ->middleware('permiso:planes.editar');
    Route::delete('/planes-academicos/{id}', [\App\Http\Controllers\Api\PlanAcademicoController::class, 'destroy'])
        ->middleware('permiso:planes.eliminar');


    Route::get('/planes-doc', [\App\Http\Controllers\Api\PlanDocController::class, 'index'])
        ->middleware('permiso:planes.ver');

    Route::get('/catalogo-academico/{catalogo}', [\App\Http\Controllers\Api\CatalogoAcademicoController::class, 'index'])
        ->middleware('permiso:estudiantes.ver');
    Route::post('/catalogo-academico/{catalogo}', [\App\Http\Controllers\Api\CatalogoAcademicoController::class, 'store'])
        ->middleware('permiso:estudiantes.crear');
    Route::get('/catalogo-academico/{catalogo}/{id}', [\App\Http\Controllers\Api\CatalogoAcademicoController::class, 'show'])
        ->middleware('permiso:estudiantes.ver');
    Route::put('/catalogo-academico/{catalogo}/{id}', [\App\Http\Controllers\Api\CatalogoAcademicoController::class, 'update'])
        ->middleware('permiso:estudiantes.editar');
    Route::delete('/catalogo-academico/{catalogo}/{id}', [\App\Http\Controllers\Api\CatalogoAcademicoController::class, 'destroy'])
        ->middleware('permiso:estudiantes.eliminar');

    Route::get('/articulos', [\App\Http\Controllers\Api\ArticuloController::class, 'index'])
        ->middleware('permiso:noticias.ver');
    Route::post('/articulos', [\App\Http\Controllers\Api\ArticuloController::class, 'store'])
        ->middleware('permiso:noticias.crear');
    Route::get('/articulos/slug/{slug}', [\App\Http\Controllers\Api\ArticuloController::class, 'showBySlug'])
        ->middleware('permiso:noticias.ver');
    Route::get('/articulos/{id}', [\App\Http\Controllers\Api\ArticuloController::class, 'show'])
        ->middleware('permiso:noticias.ver');
    Route::put('/articulos/{id}', [\App\Http\Controllers\Api\ArticuloController::class, 'update'])
        ->middleware('permiso:noticias.editar');
    Route::delete('/articulos/{id}', [\App\Http\Controllers\Api\ArticuloController::class, 'destroy'])
        ->middleware('permiso:noticias.eliminar');

    Route::get('/boletines', [\App\Http\Controllers\Api\BoletinController::class, 'index'])
        ->middleware('permiso:noticias.ver');
    Route::post('/boletines', [\App\Http\Controllers\Api\BoletinController::class, 'store'])
        ->middleware('permiso:noticias.crear');
    Route::get('/boletines/{id}', [\App\Http\Controllers\Api\BoletinController::class, 'show'])
        ->middleware('permiso:noticias.ver');
    Route::put('/boletines/{id}', [\App\Http\Controllers\Api\BoletinController::class, 'update'])
        ->middleware('permiso:noticias.editar');
    Route::delete('/boletines/{id}', [\App\Http\Controllers\Api\BoletinController::class, 'destroy'])
        ->middleware('permiso:noticias.eliminar');

    Route::get('/fotos', [\App\Http\Controllers\Api\FotoController::class, 'index'])
        ->middleware('permiso:contenido.ver');
    Route::post('/fotos', [\App\Http\Controllers\Api\FotoController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/fotos/{id}', [\App\Http\Controllers\Api\FotoController::class, 'show'])
        ->middleware('permiso:contenido.ver');
    Route::put('/fotos/{id}', [\App\Http\Controllers\Api\FotoController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/fotos/{id}', [\App\Http\Controllers\Api\FotoController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/tesis', [\App\Http\Controllers\Api\TesisController::class, 'index'])
        ->middleware('permiso:biblioteca.ver');
    Route::post('/tesis', [\App\Http\Controllers\Api\TesisController::class, 'store'])
        ->middleware('permiso:biblioteca.crear');
    Route::get('/tesis/{id}', [\App\Http\Controllers\Api\TesisController::class, 'show'])
        ->middleware('permiso:biblioteca.ver');
    Route::put('/tesis/{id}', [\App\Http\Controllers\Api\TesisController::class, 'update'])
        ->middleware('permiso:biblioteca.editar');
    Route::delete('/tesis/{id}', [\App\Http\Controllers\Api\TesisController::class, 'destroy'])
        ->middleware('permiso:biblioteca.eliminar');

    Route::get('/monografias', [\App\Http\Controllers\Api\MonografiaController::class, 'index'])
        ->middleware('permiso:biblioteca.ver');
    Route::post('/monografias', [\App\Http\Controllers\Api\MonografiaController::class, 'store'])
        ->middleware('permiso:biblioteca.crear');
    Route::get('/monografias/{id}', [\App\Http\Controllers\Api\MonografiaController::class, 'show'])
        ->middleware('permiso:biblioteca.ver');
    Route::put('/monografias/{id}', [\App\Http\Controllers\Api\MonografiaController::class, 'update'])
        ->middleware('permiso:biblioteca.editar');
    Route::delete('/monografias/{id}', [\App\Http\Controllers\Api\MonografiaController::class, 'destroy'])
        ->middleware('permiso:biblioteca.eliminar');

    Route::get('/revistas', [\App\Http\Controllers\Api\RevistaController::class, 'index'])
        ->middleware('permiso:biblioteca.ver');
    Route::post('/revistas', [\App\Http\Controllers\Api\RevistaController::class, 'store'])
        ->middleware('permiso:biblioteca.crear');
    Route::get('/revistas/{id}', [\App\Http\Controllers\Api\RevistaController::class, 'show'])
        ->middleware('permiso:biblioteca.ver');
    Route::put('/revistas/{id}', [\App\Http\Controllers\Api\RevistaController::class, 'update'])
        ->middleware('permiso:biblioteca.editar');
    Route::delete('/revistas/{id}', [\App\Http\Controllers\Api\RevistaController::class, 'destroy'])
        ->middleware('permiso:biblioteca.eliminar');

    Route::get('/revistas-cientificas', [\App\Http\Controllers\Api\RevistaCientificaController::class, 'index'])
        ->middleware('permiso:biblioteca.ver');
    Route::post('/revistas-cientificas', [\App\Http\Controllers\Api\RevistaCientificaController::class, 'store'])
        ->middleware('permiso:biblioteca.crear');
    Route::get('/revistas-cientificas/{id}', [\App\Http\Controllers\Api\RevistaCientificaController::class, 'show'])
        ->middleware('permiso:biblioteca.ver');
    Route::put('/revistas-cientificas/{id}', [\App\Http\Controllers\Api\RevistaCientificaController::class, 'update'])
        ->middleware('permiso:biblioteca.editar');
    Route::delete('/revistas-cientificas/{id}', [\App\Http\Controllers\Api\RevistaCientificaController::class, 'destroy'])
        ->middleware('permiso:biblioteca.eliminar');

    Route::get('/programas-academicos', [\App\Http\Controllers\Api\ProgramaAcademicoController::class, 'index'])
        ->middleware('permiso:programas.ver');
    Route::post('/programas-academicos', [\App\Http\Controllers\Api\ProgramaAcademicoController::class, 'store'])
        ->middleware('permiso:programas.crear');
    Route::get('/programas-academicos/{id}', [\App\Http\Controllers\Api\ProgramaAcademicoController::class, 'show'])
        ->middleware('permiso:programas.ver');
    Route::put('/programas-academicos/{id}', [\App\Http\Controllers\Api\ProgramaAcademicoController::class, 'update'])
        ->middleware('permiso:programas.editar');
    Route::delete('/programas-academicos/{id}', [\App\Http\Controllers\Api\ProgramaAcademicoController::class, 'destroy'])
        ->middleware('permiso:programas.eliminar');

    // Imparticiones (versiones) de un programa
    Route::get('/programas-academicos/{id}/imparticiones', [\App\Http\Controllers\Api\ProgramaAcademicoController::class, 'imparticiones'])
        ->middleware('permiso:programas.ver');
    Route::post('/programas-academicos/{id}/imparticiones', [\App\Http\Controllers\Api\ProgramaAcademicoController::class, 'storeImparticion'])
        ->middleware('permiso:programas.editar');
    Route::put('/programas-academicos/{id}/imparticiones/{id_imp}', [\App\Http\Controllers\Api\ProgramaAcademicoController::class, 'updateImparticion'])
        ->middleware('permiso:programas.editar');

    // Planes habilitados para un programa
    Route::get('/programas-academicos/{id}/planes', [\App\Http\Controllers\Api\ProgramaAcademicoController::class, 'planes'])
        ->middleware('permiso:programas.ver');
    Route::post('/programas-academicos/{id}/planes', [\App\Http\Controllers\Api\ProgramaAcademicoController::class, 'syncPlanes'])
        ->middleware('permiso:programas.editar');

    Route::get('/tipos-postgrado', [\App\Http\Controllers\Api\TipoPostgradoController::class, 'index'])
        ->middleware('permiso:programas.ver');
    Route::post('/tipos-postgrado', [\App\Http\Controllers\Api\TipoPostgradoController::class, 'store'])
        ->middleware('permiso:programas.crear');
    Route::get('/tipos-postgrado/{id}', [\App\Http\Controllers\Api\TipoPostgradoController::class, 'show'])
        ->middleware('permiso:programas.ver');
    Route::put('/tipos-postgrado/{id}', [\App\Http\Controllers\Api\TipoPostgradoController::class, 'update'])
        ->middleware('permiso:programas.editar');
    Route::delete('/tipos-postgrado/{id}', [\App\Http\Controllers\Api\TipoPostgradoController::class, 'destroy'])
        ->middleware('permiso:programas.eliminar');

    Route::get('/fechas-pago', [\App\Http\Controllers\Api\FechaPagoController::class, 'index'])
        ->middleware('permiso:pagos.ver');
    Route::post('/fechas-pago', [\App\Http\Controllers\Api\FechaPagoController::class, 'store'])
        ->middleware('permiso:pagos.crear');
    Route::post('/fechas-pago/generar-lote', [\App\Http\Controllers\Api\FechaPagoController::class, 'generarLote'])
        ->middleware('permiso:pagos.crear');
    Route::get('/fechas-pago/{id}', [\App\Http\Controllers\Api\FechaPagoController::class, 'show'])
        ->middleware('permiso:pagos.ver');
    Route::put('/fechas-pago/{id}', [\App\Http\Controllers\Api\FechaPagoController::class, 'update'])
        ->middleware('permiso:pagos.editar');
    Route::delete('/fechas-pago/{id}', [\App\Http\Controllers\Api\FechaPagoController::class, 'destroy'])
        ->middleware('permiso:pagos.eliminar');

    Route::post('/portal/pago/iniciar', [\App\Http\Controllers\Api\PagoOnlineSessionController::class, 'iniciar'])
        ->middleware('permiso:pagos.editar');
    Route::get('/portal/pago/estado/{session_id}', [\App\Http\Controllers\Api\PagoOnlineSessionController::class, 'estado'])
        ->middleware('permiso:pagos.ver');

    Route::get('/fechas-doc', [\App\Http\Controllers\Api\FechaDocController::class, 'index'])
        ->middleware('permiso:documentos.ver');
    Route::post('/fechas-doc', [\App\Http\Controllers\Api\FechaDocController::class, 'store'])
        ->middleware('permiso:documentos.crear');
    Route::get('/fechas-doc/{id}', [\App\Http\Controllers\Api\FechaDocController::class, 'show'])
        ->middleware('permiso:documentos.ver');
    Route::put('/fechas-doc/{id}', [\App\Http\Controllers\Api\FechaDocController::class, 'update'])
        ->middleware('permiso:documentos.editar');
    Route::delete('/fechas-doc/{id}', [\App\Http\Controllers\Api\FechaDocController::class, 'destroy'])
        ->middleware('permiso:documentos.eliminar');

    Route::get('/documentos-academicos', [\App\Http\Controllers\Api\DocumentoAcademicoController::class, 'index'])
        ->middleware('permiso:documentos.ver');
    Route::post('/documentos-academicos', [\App\Http\Controllers\Api\DocumentoAcademicoController::class, 'store'])
        ->middleware('permiso:documentos.crear');
    Route::get('/documentos-academicos/{id}', [\App\Http\Controllers\Api\DocumentoAcademicoController::class, 'show'])
        ->middleware('permiso:documentos.ver');
    Route::put('/documentos-academicos/{id}', [\App\Http\Controllers\Api\DocumentoAcademicoController::class, 'update'])
        ->middleware('permiso:documentos.editar');
    Route::delete('/documentos-academicos/{id}', [\App\Http\Controllers\Api\DocumentoAcademicoController::class, 'destroy'])
        ->middleware('permiso:documentos.eliminar');

    Route::get('/ayudas', [\App\Http\Controllers\Api\AyudaController::class, 'index'])
        ->middleware('permiso:contenido.ver');
    Route::post('/ayudas', [\App\Http\Controllers\Api\AyudaController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/ayudas/{id}', [\App\Http\Controllers\Api\AyudaController::class, 'show'])
        ->middleware('permiso:contenido.ver');
    Route::put('/ayudas/{id}', [\App\Http\Controllers\Api\AyudaController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/ayudas/{id}', [\App\Http\Controllers\Api\AyudaController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/cartas', [\App\Http\Controllers\Api\CartaController::class, 'index'])
        ->middleware('permiso:cartas.ver');
    Route::post('/cartas', [\App\Http\Controllers\Api\CartaController::class, 'store'])
        ->middleware('permiso:cartas.crear');
    Route::get('/cartas/{id}', [\App\Http\Controllers\Api\CartaController::class, 'show'])
        ->middleware('permiso:cartas.ver');
    Route::put('/cartas/{id}', [\App\Http\Controllers\Api\CartaController::class, 'update'])
        ->middleware('permiso:cartas.editar');
    Route::delete('/cartas/{id}', [\App\Http\Controllers\Api\CartaController::class, 'destroy'])
        ->middleware('permiso:cartas.eliminar');

    Route::get('/cartas-modelo', [\App\Http\Controllers\Api\CartaModeloController::class, 'index'])
        ->middleware('permiso:cartas.ver');
    Route::post('/cartas-modelo', [\App\Http\Controllers\Api\CartaModeloController::class, 'store'])
        ->middleware('permiso:cartas.crear');
    Route::get('/cartas-modelo/{id}', [\App\Http\Controllers\Api\CartaModeloController::class, 'show'])
        ->middleware('permiso:cartas.ver');
    Route::put('/cartas-modelo/{id}', [\App\Http\Controllers\Api\CartaModeloController::class, 'update'])
        ->middleware('permiso:cartas.editar');
    Route::delete('/cartas-modelo/{id}', [\App\Http\Controllers\Api\CartaModeloController::class, 'destroy'])
        ->middleware('permiso:cartas.eliminar');

    Route::get('/cartas-generadas', [\App\Http\Controllers\Api\CartaGenController::class, 'index'])
        ->middleware('permiso:cartas.ver');
    Route::post('/cartas-generadas', [\App\Http\Controllers\Api\CartaGenController::class, 'store'])
        ->middleware('permiso:cartas.crear');
    Route::get('/cartas-generadas/{id}', [\App\Http\Controllers\Api\CartaGenController::class, 'show'])
        ->middleware('permiso:cartas.ver');
    Route::put('/cartas-generadas/{id}', [\App\Http\Controllers\Api\CartaGenController::class, 'update'])
        ->middleware('permiso:cartas.editar');
    Route::delete('/cartas-generadas/{id}', [\App\Http\Controllers\Api\CartaGenController::class, 'destroy'])
        ->middleware('permiso:cartas.eliminar');

    Route::get('/formularios-academicos', [\App\Http\Controllers\Api\FormularioAcademicoController::class, 'index'])
        ->middleware('permiso:configuracion.ver');
    Route::post('/formularios-academicos', [\App\Http\Controllers\Api\FormularioAcademicoController::class, 'store'])
        ->middleware('permiso:configuracion.crear');
    Route::get('/formularios-academicos/{id}', [\App\Http\Controllers\Api\FormularioAcademicoController::class, 'show'])
        ->middleware('permiso:configuracion.ver');
    Route::put('/formularios-academicos/{id}', [\App\Http\Controllers\Api\FormularioAcademicoController::class, 'update'])
        ->middleware('permiso:configuracion.editar');
    Route::delete('/formularios-academicos/{id}', [\App\Http\Controllers\Api\FormularioAcademicoController::class, 'destroy'])
        ->middleware('permiso:configuracion.eliminar');

    Route::get('/formularios-ins', [\App\Http\Controllers\Api\FormularioInsController::class, 'index'])
        ->middleware('permiso:configuracion.ver');
    Route::post('/formularios-ins', [\App\Http\Controllers\Api\FormularioInsController::class, 'store'])
        ->middleware('permiso:configuracion.crear');
    Route::get('/formularios-ins/{id}', [\App\Http\Controllers\Api\FormularioInsController::class, 'show'])
        ->middleware('permiso:configuracion.ver');
    Route::put('/formularios-ins/{id}', [\App\Http\Controllers\Api\FormularioInsController::class, 'update'])
        ->middleware('permiso:configuracion.editar');
    Route::delete('/formularios-ins/{id}', [\App\Http\Controllers\Api\FormularioInsController::class, 'destroy'])
        ->middleware('permiso:configuracion.eliminar');

    Route::get('/tests-academicos', [\App\Http\Controllers\Api\TestAcademicoController::class, 'index'])
        ->middleware('permiso:notas.ver');
    Route::post('/tests-academicos', [\App\Http\Controllers\Api\TestAcademicoController::class, 'store'])
        ->middleware('permiso:notas.crear');
    Route::get('/tests-academicos/{id}', [\App\Http\Controllers\Api\TestAcademicoController::class, 'show'])
        ->middleware('permiso:notas.ver');
    Route::put('/tests-academicos/{id}', [\App\Http\Controllers\Api\TestAcademicoController::class, 'update'])
        ->middleware('permiso:notas.editar');
    Route::delete('/tests-academicos/{id}', [\App\Http\Controllers\Api\TestAcademicoController::class, 'destroy'])
        ->middleware('permiso:notas.eliminar');

    Route::get('/grupos-academicos', [\App\Http\Controllers\Api\GrupoAcademicoController::class, 'index'])
        ->middleware('permiso:programas.ver');
    Route::post('/grupos-academicos', [\App\Http\Controllers\Api\GrupoAcademicoController::class, 'store'])
        ->middleware('permiso:programas.crear');
    Route::get('/grupos-academicos/{id}', [\App\Http\Controllers\Api\GrupoAcademicoController::class, 'show'])
        ->middleware('permiso:programas.ver');
    Route::put('/grupos-academicos/{id}', [\App\Http\Controllers\Api\GrupoAcademicoController::class, 'update'])
        ->middleware('permiso:programas.editar');
    Route::delete('/grupos-academicos/{id}', [\App\Http\Controllers\Api\GrupoAcademicoController::class, 'destroy'])
        ->middleware('permiso:programas.eliminar');

    Route::get('/historial', [\App\Http\Controllers\Api\HistorialController::class, 'index'])
        ->middleware('permiso:historial.ver');
    Route::post('/historial', [\App\Http\Controllers\Api\HistorialController::class, 'store'])
        ->middleware('permiso:historial.crear');
    Route::get('/historial/{id}', [\App\Http\Controllers\Api\HistorialController::class, 'show'])
        ->middleware('permiso:historial.ver');
    Route::put('/historial/{id}', [\App\Http\Controllers\Api\HistorialController::class, 'update'])
        ->middleware('permiso:historial.editar');
    Route::delete('/historial/{id}', [\App\Http\Controllers\Api\HistorialController::class, 'destroy'])
        ->middleware('permiso:historial.eliminar');

    Route::get('/hojas-evaluacion', [\App\Http\Controllers\Api\HojaEvaluacionController::class, 'index'])
        ->middleware('permiso:notas.ver');
    Route::post('/hojas-evaluacion', [\App\Http\Controllers\Api\HojaEvaluacionController::class, 'store'])
        ->middleware('permiso:notas.crear');
    Route::get('/hojas-evaluacion/{id}', [\App\Http\Controllers\Api\HojaEvaluacionController::class, 'show'])
        ->middleware('permiso:notas.ver');
    Route::put('/hojas-evaluacion/{id}', [\App\Http\Controllers\Api\HojaEvaluacionController::class, 'update'])
        ->middleware('permiso:notas.editar');
    Route::delete('/hojas-evaluacion/{id}', [\App\Http\Controllers\Api\HojaEvaluacionController::class, 'destroy'])
        ->middleware('permiso:notas.eliminar');

    Route::get('/moodles', [\App\Http\Controllers\Api\MoodleController::class, 'index'])
        ->middleware('permiso:moodle.ver');
    Route::post('/moodles', [\App\Http\Controllers\Api\MoodleController::class, 'store'])
        ->middleware('permiso:moodle.crear');
    Route::get('/moodles/{id}', [\App\Http\Controllers\Api\MoodleController::class, 'show'])
        ->middleware('permiso:moodle.ver');
    Route::put('/moodles/{id}', [\App\Http\Controllers\Api\MoodleController::class, 'update'])
        ->middleware('permiso:moodle.editar');
    Route::delete('/moodles/{id}', [\App\Http\Controllers\Api\MoodleController::class, 'destroy'])
        ->middleware('permiso:moodle.eliminar');

    Route::get('/mdl-courses', [\App\Http\Controllers\Api\MdlCourseController::class, 'index'])
        ->middleware('permiso:moodle.ver');
    Route::post('/mdl-courses', [\App\Http\Controllers\Api\MdlCourseController::class, 'store'])
        ->middleware('permiso:moodle.crear');
    Route::get('/mdl-courses/{id}', [\App\Http\Controllers\Api\MdlCourseController::class, 'show'])
        ->middleware('permiso:moodle.ver');
    Route::put('/mdl-courses/{id}', [\App\Http\Controllers\Api\MdlCourseController::class, 'update'])
        ->middleware('permiso:moodle.editar');
    Route::delete('/mdl-courses/{id}', [\App\Http\Controllers\Api\MdlCourseController::class, 'destroy'])
        ->middleware('permiso:moodle.eliminar');

    Route::get('/mdl-users', [\App\Http\Controllers\Api\MdlUserController::class, 'index'])
        ->middleware('permiso:moodle.ver');
    Route::post('/mdl-users', [\App\Http\Controllers\Api\MdlUserController::class, 'store'])
        ->middleware('permiso:moodle.crear');
    Route::get('/mdl-users/{id}', [\App\Http\Controllers\Api\MdlUserController::class, 'show'])
        ->middleware('permiso:moodle.ver');
    Route::put('/mdl-users/{id}', [\App\Http\Controllers\Api\MdlUserController::class, 'update'])
        ->middleware('permiso:moodle.editar');
    Route::delete('/mdl-users/{id}', [\App\Http\Controllers\Api\MdlUserController::class, 'destroy'])
        ->middleware('permiso:moodle.eliminar');

    Route::get('/ingresos/resumen', [\App\Http\Controllers\Api\IngresoController::class, 'resumen'])
        ->middleware('permiso:reportes.ver');
    Route::get('/ingresos', [\App\Http\Controllers\Api\IngresoController::class, 'index'])
        ->middleware('permiso:reportes.ver');

    Route::get('/ventas/reporte', [\App\Http\Controllers\Api\VentaController::class, 'reporte'])
        ->middleware('permiso:ventas.ver');
    Route::get('/ventas/reporte/por-vendedor', [\App\Http\Controllers\Api\VentaController::class, 'reportePorVendedor'])
        ->middleware('permiso:ventas.ver');
    Route::get('/ventas/reporte/por-canal', [\App\Http\Controllers\Api\VentaController::class, 'reportePorCanal'])
        ->middleware('permiso:ventas.ver');
    Route::get('/ventas/reporte/proyeccion', [\App\Http\Controllers\Api\VentaController::class, 'proyeccionCobros'])
        ->middleware('permiso:ventas.ver');
    Route::get('/ventas', [\App\Http\Controllers\Api\VentaController::class, 'index'])
        ->middleware('permiso:ventas.ver');
    Route::get('/ventas/{id}', [\App\Http\Controllers\Api\VentaController::class, 'show'])
        ->middleware('permiso:ventas.ver');
    Route::get('/ventas/{id}/pdf', [\App\Http\Controllers\Api\VentaController::class, 'pdf'])
        ->middleware('permiso:ventas.ver');
    Route::post('/ventas/{id}/enviar-correo', [\App\Http\Controllers\Api\VentaController::class, 'enviarCorreo'])
        ->middleware('permiso:ventas.ver');

    Route::get('/correos-enviados', [\App\Http\Controllers\Api\CorreoEnviadoController::class, 'index'])
        ->middleware('permiso:ventas.ver');

    Route::get('/reglamentos/{idPrograma}', [\App\Http\Controllers\Api\ReglamentoController::class, 'show'])
        ->middleware('permiso:programas.ver');
    Route::put('/reglamentos/{idPrograma}', [\App\Http\Controllers\Api\ReglamentoController::class, 'update'])
        ->middleware('permiso:programas.editar');

    Route::get('/sueldos-docentes/docentes', [\App\Http\Controllers\Api\SueldoDocenteController::class, 'docentes'])
        ->middleware('permiso:docentes.ver');
    Route::get('/sueldos-docentes/imparticiones', [\App\Http\Controllers\Api\SueldoDocenteController::class, 'imparticiones'])
        ->middleware('permiso:docentes.ver');
    Route::get('/sueldos-docentes', [\App\Http\Controllers\Api\SueldoDocenteController::class, 'index'])
        ->middleware('permiso:docentes.ver');
    Route::post('/sueldos-docentes', [\App\Http\Controllers\Api\SueldoDocenteController::class, 'store'])
        ->middleware('permiso:docentes.crear');
    Route::get('/sueldos-docentes/{id}', [\App\Http\Controllers\Api\SueldoDocenteController::class, 'show'])
        ->middleware('permiso:docentes.ver');
    Route::match(['put','post'], '/sueldos-docentes/{id}', [\App\Http\Controllers\Api\SueldoDocenteController::class, 'update']);
    Route::delete('/sueldos-docentes/{id}', [\App\Http\Controllers\Api\SueldoDocenteController::class, 'destroy'])
        ->middleware('permiso:docentes.eliminar');
    Route::post('/sueldos-docentes/{id}/pagos/lote', [\App\Http\Controllers\Api\SueldoDocenteController::class, 'storePagoLote'])
        ->middleware('permiso:docentes.editar');
    Route::post('/sueldos-docentes/{id}/pagos', [\App\Http\Controllers\Api\SueldoDocenteController::class, 'storePago'])
        ->middleware('permiso:docentes.editar');
    Route::delete('/sueldos-docentes/{id}/pagos/{pagoId}', [\App\Http\Controllers\Api\SueldoDocenteController::class, 'destroyPago'])
        ->middleware('permiso:docentes.eliminar');

    Route::get('/speeches-ventas/categorias', [\App\Http\Controllers\Api\SpeechVentasController::class, 'categorias'])
        ->middleware('permiso:ventas.ver');
    Route::get('/speeches-ventas', [\App\Http\Controllers\Api\SpeechVentasController::class, 'index'])
        ->middleware('permiso:ventas.ver');
    Route::post('/speeches-ventas', [\App\Http\Controllers\Api\SpeechVentasController::class, 'store'])
        ->middleware('permiso:ventas.crear');
    Route::get('/speeches-ventas/{id}', [\App\Http\Controllers\Api\SpeechVentasController::class, 'show'])
        ->middleware('permiso:ventas.ver');
    Route::put('/speeches-ventas/{id}', [\App\Http\Controllers\Api\SpeechVentasController::class, 'update'])
        ->middleware('permiso:ventas.editar');
    Route::delete('/speeches-ventas/{id}', [\App\Http\Controllers\Api\SpeechVentasController::class, 'destroy'])
        ->middleware('permiso:ventas.eliminar');
    Route::patch('/speeches-ventas/{id}/toggle', [\App\Http\Controllers\Api\SpeechVentasController::class, 'toggleActivo'])
        ->middleware('permiso:ventas.editar');

    Route::get('/efectos-especiales', [\App\Http\Controllers\Api\EfectoEspecialController::class, 'index'])
        ->middleware('permiso:configuracion.ver');
    Route::post('/efectos-especiales', [\App\Http\Controllers\Api\EfectoEspecialController::class, 'store'])
        ->middleware('permiso:configuracion.crear');
    Route::get('/efectos-especiales/{id}', [\App\Http\Controllers\Api\EfectoEspecialController::class, 'show'])
        ->middleware('permiso:configuracion.ver');
    Route::put('/efectos-especiales/{id}', [\App\Http\Controllers\Api\EfectoEspecialController::class, 'update'])
        ->middleware('permiso:configuracion.editar');
    Route::delete('/efectos-especiales/{id}', [\App\Http\Controllers\Api\EfectoEspecialController::class, 'destroy'])
        ->middleware('permiso:configuracion.eliminar');

    Route::get('/vendedores', [\App\Http\Controllers\Api\VendedorController::class, 'index'])
        ->middleware('permiso:ventas.ver');
    Route::post('/vendedores', [\App\Http\Controllers\Api\VendedorController::class, 'store'])
        ->middleware('permiso:ventas.crear');
    Route::get('/vendedores/{id}/comision-detalle', [\App\Http\Controllers\Api\VendedorController::class, 'comisionDetalle'])
        ->middleware('permiso:ventas.ver');
    Route::get('/vendedores/{id}/comision-detalle/pdf', [\App\Http\Controllers\Api\VendedorController::class, 'comisionDetallePdf'])
        ->middleware('permiso:ventas.ver');
    Route::get('/vendedores/{id}', [\App\Http\Controllers\Api\VendedorController::class, 'show'])
        ->middleware('permiso:ventas.ver');
    Route::put('/vendedores/{id}', [\App\Http\Controllers\Api\VendedorController::class, 'update'])
        ->middleware('permiso:ventas.editar');
    Route::delete('/vendedores/{id}', [\App\Http\Controllers\Api\VendedorController::class, 'destroy'])
        ->middleware('permiso:ventas.eliminar');

    
    Route::get('/comisiones/sugerida', [\App\Http\Controllers\Api\ComisionController::class, 'sugerida'])
        ->middleware('permiso:comisiones.ver');
    Route::get('/comisiones', [\App\Http\Controllers\Api\ComisionController::class, 'index'])
        ->middleware('permiso:comisiones.ver');
    Route::get('/comisiones/{id}', [\App\Http\Controllers\Api\ComisionController::class, 'show'])
        ->middleware('permiso:comisiones.ver');
    Route::post('/comisiones', [\App\Http\Controllers\Api\ComisionController::class, 'store'])
        ->middleware('permiso:comisiones.crear');
    Route::patch('/comisiones/{id}/aprobar', [\App\Http\Controllers\Api\ComisionController::class, 'aprobar'])
        ->middleware('permiso:comisiones.aprobar');
    Route::patch('/comisiones/{id}/pagar', [\App\Http\Controllers\Api\ComisionController::class, 'pagar'])
        ->middleware('permiso:comisiones.pagar');
    Route::patch('/comisiones/{id}/anular', [\App\Http\Controllers\Api\ComisionController::class, 'anular'])
        ->middleware('permiso:comisiones.aprobar');

    
    Route::get('/compromisos-cobro/resumen', [\App\Http\Controllers\Api\CompromisoCobroController::class, 'resumen'])
        ->middleware('permiso:compromisos-cobro.ver');
    Route::get('/compromisos-cobro', [\App\Http\Controllers\Api\CompromisoCobroController::class, 'index'])
        ->middleware('permiso:compromisos-cobro.ver');
    Route::get('/compromisos-cobro/{id}/historial', [\App\Http\Controllers\Api\CompromisoCobroController::class, 'historial'])
        ->middleware('permiso:compromisos-cobro.ver');
    Route::post('/compromisos-cobro', [\App\Http\Controllers\Api\CompromisoCobroController::class, 'store'])
        ->middleware('permiso:compromisos-cobro.crear');
    Route::patch('/compromisos-cobro/{id}/reprogramar', [\App\Http\Controllers\Api\CompromisoCobroController::class, 'reprogramar'])
        ->middleware('permiso:compromisos-cobro.editar');
    Route::patch('/compromisos-cobro/{id}/cumplir', [\App\Http\Controllers\Api\CompromisoCobroController::class, 'cumplir'])
        ->middleware('permiso:compromisos-cobro.editar');
    Route::patch('/compromisos-cobro/{id}/cancelar', [\App\Http\Controllers\Api\CompromisoCobroController::class, 'cancelar'])
        ->middleware('permiso:compromisos-cobro.editar');

    Route::get('/intents', [\App\Http\Controllers\Api\IntentController::class, 'index'])
        ->middleware('permiso:whatsapp.ver');
    Route::post('/intents', [\App\Http\Controllers\Api\IntentController::class, 'store'])
        ->middleware('permiso:whatsapp.crear');
    Route::get('/intents/{id}', [\App\Http\Controllers\Api\IntentController::class, 'show'])
        ->middleware('permiso:whatsapp.ver');
    Route::put('/intents/{id}', [\App\Http\Controllers\Api\IntentController::class, 'update'])
        ->middleware('permiso:whatsapp.editar');
    Route::delete('/intents/{id}', [\App\Http\Controllers\Api\IntentController::class, 'destroy'])
        ->middleware('permiso:whatsapp.eliminar');
    Route::patch('/intents/{id}/toggle', [\App\Http\Controllers\Api\IntentController::class, 'toggleActivo'])
        ->middleware('permiso:whatsapp.editar');

    Route::get('/usuarios-moodle', [\App\Http\Controllers\Api\UsuarioMoodleController::class, 'index'])
        ->middleware('permiso:moodle.ver');
    Route::post('/usuarios-moodle', [\App\Http\Controllers\Api\UsuarioMoodleController::class, 'store'])
        ->middleware('permiso:moodle.crear');
    Route::get('/usuarios-moodle/{id}', [\App\Http\Controllers\Api\UsuarioMoodleController::class, 'show'])
        ->middleware('permiso:moodle.ver');
    Route::put('/usuarios-moodle/{id}', [\App\Http\Controllers\Api\UsuarioMoodleController::class, 'update'])
        ->middleware('permiso:moodle.editar');
    Route::delete('/usuarios-moodle/{id}', [\App\Http\Controllers\Api\UsuarioMoodleController::class, 'destroy'])
        ->middleware('permiso:moodle.eliminar');

    Route::get('/usuarios-plan', [\App\Http\Controllers\Api\UsuarioPlanController::class, 'index'])
        ->middleware('permiso:estudiantes.ver');
    Route::post('/usuarios-plan', [\App\Http\Controllers\Api\UsuarioPlanController::class, 'store'])
        ->middleware('permiso:estudiantes.crear');
    Route::get('/usuarios-plan/{id}', [\App\Http\Controllers\Api\UsuarioPlanController::class, 'show'])
        ->middleware('permiso:estudiantes.ver');
    Route::put('/usuarios-plan/{id}', [\App\Http\Controllers\Api\UsuarioPlanController::class, 'update'])
        ->middleware('permiso:estudiantes.editar');
    Route::delete('/usuarios-plan/{id}', [\App\Http\Controllers\Api\UsuarioPlanController::class, 'destroy'])
        ->middleware('permiso:estudiantes.eliminar');

    Route::get('/usuarios-plandoc', [\App\Http\Controllers\Api\UsuarioPlanDocController::class, 'index'])
        ->middleware('permiso:estudiantes.ver');
    Route::post('/usuarios-plandoc', [\App\Http\Controllers\Api\UsuarioPlanDocController::class, 'store'])
        ->middleware('permiso:estudiantes.crear');
    Route::get('/usuarios-plandoc/{id}', [\App\Http\Controllers\Api\UsuarioPlanDocController::class, 'show'])
        ->middleware('permiso:estudiantes.ver');
    Route::put('/usuarios-plandoc/{id}', [\App\Http\Controllers\Api\UsuarioPlanDocController::class, 'update'])
        ->middleware('permiso:estudiantes.editar');
    Route::delete('/usuarios-plandoc/{id}', [\App\Http\Controllers\Api\UsuarioPlanDocController::class, 'destroy'])
        ->middleware('permiso:estudiantes.eliminar');

    Route::get('/usuarios-programa', [\App\Http\Controllers\Api\UsuarioProgramaController::class, 'index'])
        ->middleware('permiso:estudiantes.ver');
    Route::post('/usuarios-programa', [\App\Http\Controllers\Api\UsuarioProgramaController::class, 'store'])
        ->middleware('permiso:estudiantes.crear');
    Route::get('/usuarios-programa/{id}', [\App\Http\Controllers\Api\UsuarioProgramaController::class, 'show'])
        ->middleware('permiso:estudiantes.ver');
    Route::put('/usuarios-programa/{id}', [\App\Http\Controllers\Api\UsuarioProgramaController::class, 'update'])
        ->middleware('permiso:estudiantes.editar');
    Route::delete('/usuarios-programa/{id}', [\App\Http\Controllers\Api\UsuarioProgramaController::class, 'destroy'])
        ->middleware('permiso:estudiantes.eliminar');

    Route::get('/usuarios-tipoprograma', [\App\Http\Controllers\Api\UsuarioTipoProgramaController::class, 'index'])
        ->middleware('permiso:estudiantes.ver');
    Route::post('/usuarios-tipoprograma', [\App\Http\Controllers\Api\UsuarioTipoProgramaController::class, 'store'])
        ->middleware('permiso:estudiantes.crear');
    Route::get('/usuarios-tipoprograma/{id}', [\App\Http\Controllers\Api\UsuarioTipoProgramaController::class, 'show'])
        ->middleware('permiso:estudiantes.ver');
    Route::put('/usuarios-tipoprograma/{id}', [\App\Http\Controllers\Api\UsuarioTipoProgramaController::class, 'update'])
        ->middleware('permiso:estudiantes.editar');
    Route::delete('/usuarios-tipoprograma/{id}', [\App\Http\Controllers\Api\UsuarioTipoProgramaController::class, 'destroy'])
        ->middleware('permiso:estudiantes.eliminar');

    Route::get('/configuracion-academica', [\App\Http\Controllers\Api\ConfiguracionAcademicaController::class, 'index'])
        ->middleware('permiso:configuracion.ver');
    Route::post('/configuracion-academica', [\App\Http\Controllers\Api\ConfiguracionAcademicaController::class, 'store'])
        ->middleware('permiso:configuracion.crear');
    Route::get('/configuracion-academica/{id}', [\App\Http\Controllers\Api\ConfiguracionAcademicaController::class, 'show'])
        ->middleware('permiso:configuracion.ver');
    Route::put('/configuracion-academica/{id}', [\App\Http\Controllers\Api\ConfiguracionAcademicaController::class, 'update'])
        ->middleware('permiso:configuracion.editar');
    Route::delete('/configuracion-academica/{id}', [\App\Http\Controllers\Api\ConfiguracionAcademicaController::class, 'destroy'])
        ->middleware('permiso:configuracion.eliminar');

    Route::get('/archivos-academicos', [\App\Http\Controllers\Api\ArchivoAcademicoController::class, 'index'])
        ->middleware('permiso:documentos.ver');
    Route::post('/archivos-academicos', [\App\Http\Controllers\Api\ArchivoAcademicoController::class, 'store'])
        ->middleware('permiso:documentos.crear');
    Route::get('/archivos-academicos/{id}', [\App\Http\Controllers\Api\ArchivoAcademicoController::class, 'show'])
        ->middleware('permiso:documentos.ver');
    Route::put('/archivos-academicos/{id}', [\App\Http\Controllers\Api\ArchivoAcademicoController::class, 'update'])
        ->middleware('permiso:documentos.editar');
    Route::delete('/archivos-academicos/{id}', [\App\Http\Controllers\Api\ArchivoAcademicoController::class, 'destroy'])
        ->middleware('permiso:documentos.eliminar');

    Route::get('/certificados-modelo', [\App\Http\Controllers\Api\CertificadoModeloController::class, 'index'])
        ->middleware('permiso:certificados.ver');
    Route::post('/certificados-modelo', [\App\Http\Controllers\Api\CertificadoModeloController::class, 'store'])
        ->middleware('permiso:certificados.crear');
    Route::get('/certificados-modelo/{id}', [\App\Http\Controllers\Api\CertificadoModeloController::class, 'show'])
        ->middleware('permiso:certificados.ver');
    Route::put('/certificados-modelo/{id}', [\App\Http\Controllers\Api\CertificadoModeloController::class, 'update'])
        ->middleware('permiso:certificados.editar');
    Route::delete('/certificados-modelo/{id}', [\App\Http\Controllers\Api\CertificadoModeloController::class, 'destroy'])
        ->middleware('permiso:certificados.eliminar');

    Route::get('/paginas-academicas', [\App\Http\Controllers\Api\PaginaAcademicoController::class, 'index'])
        ->middleware('permiso:web.ver');
    Route::post('/paginas-academicas', [\App\Http\Controllers\Api\PaginaAcademicoController::class, 'store'])
        ->middleware('permiso:web.crear');
    Route::get('/paginas-academicas/{id}', [\App\Http\Controllers\Api\PaginaAcademicoController::class, 'show'])
        ->middleware('permiso:web.ver');
    Route::put('/paginas-academicas/{id}', [\App\Http\Controllers\Api\PaginaAcademicoController::class, 'update'])
        ->middleware('permiso:web.editar');
    Route::delete('/paginas-academicas/{id}', [\App\Http\Controllers\Api\PaginaAcademicoController::class, 'destroy'])
        ->middleware('permiso:web.eliminar');

    Route::get('/bloques-ajustables', [\App\Http\Controllers\Api\BloqueAjustableController::class, 'index'])
        ->middleware('permiso:web.ver');
    Route::post('/bloques-ajustables', [\App\Http\Controllers\Api\BloqueAjustableController::class, 'store'])
        ->middleware('permiso:web.crear');
    Route::get('/bloques-ajustables/{id}', [\App\Http\Controllers\Api\BloqueAjustableController::class, 'show'])
        ->middleware('permiso:web.ver');
    Route::put('/bloques-ajustables/{id}', [\App\Http\Controllers\Api\BloqueAjustableController::class, 'update'])
        ->middleware('permiso:web.editar');
    Route::delete('/bloques-ajustables/{id}', [\App\Http\Controllers\Api\BloqueAjustableController::class, 'destroy'])
        ->middleware('permiso:web.eliminar');

    Route::get('/bloques-plantilla', [\App\Http\Controllers\Api\BloquePlantillaController::class, 'index'])
        ->middleware('permiso:web.ver');
    Route::post('/bloques-plantilla', [\App\Http\Controllers\Api\BloquePlantillaController::class, 'store'])
        ->middleware('permiso:web.crear');
    Route::get('/bloques-plantilla/{id}', [\App\Http\Controllers\Api\BloquePlantillaController::class, 'show'])
        ->middleware('permiso:web.ver');
    Route::put('/bloques-plantilla/{id}', [\App\Http\Controllers\Api\BloquePlantillaController::class, 'update'])
        ->middleware('permiso:web.editar');
    Route::delete('/bloques-plantilla/{id}', [\App\Http\Controllers\Api\BloquePlantillaController::class, 'destroy'])
        ->middleware('permiso:web.eliminar');

    Route::get('/secciones-bloque', [\App\Http\Controllers\Api\SeccionBloqueController::class, 'index'])
        ->middleware('permiso:web.ver');
    Route::post('/secciones-bloque', [\App\Http\Controllers\Api\SeccionBloqueController::class, 'store'])
        ->middleware('permiso:web.crear');
    Route::get('/secciones-bloque/{id}', [\App\Http\Controllers\Api\SeccionBloqueController::class, 'show'])
        ->middleware('permiso:web.ver');
    Route::put('/secciones-bloque/{id}', [\App\Http\Controllers\Api\SeccionBloqueController::class, 'update'])
        ->middleware('permiso:web.editar');
    Route::delete('/secciones-bloque/{id}', [\App\Http\Controllers\Api\SeccionBloqueController::class, 'destroy'])
        ->middleware('permiso:web.eliminar');

    Route::get('/formatos-hoja-solicitud', [\App\Http\Controllers\Api\FormatoHojaSolicitudController::class, 'index'])
        ->middleware('permiso:usuarios.ver');
    Route::post('/formatos-hoja-solicitud', [\App\Http\Controllers\Api\FormatoHojaSolicitudController::class, 'store'])
        ->middleware('permiso:usuarios.crear');
    Route::get('/formatos-hoja-solicitud/{id}', [\App\Http\Controllers\Api\FormatoHojaSolicitudController::class, 'show'])
        ->middleware('permiso:usuarios.ver');
    Route::put('/formatos-hoja-solicitud/{id}', [\App\Http\Controllers\Api\FormatoHojaSolicitudController::class, 'update'])
        ->middleware('permiso:usuarios.editar');
    Route::delete('/formatos-hoja-solicitud/{id}', [\App\Http\Controllers\Api\FormatoHojaSolicitudController::class, 'destroy'])
        ->middleware('permiso:usuarios.eliminar');

    Route::get('/menus-academicos', [\App\Http\Controllers\Api\MenuAcademicoController::class, 'index'])
        ->middleware('permiso:configuracion.ver');
    Route::post('/menus-academicos', [\App\Http\Controllers\Api\MenuAcademicoController::class, 'store'])
        ->middleware('permiso:configuracion.crear');
    Route::get('/menus-academicos/{id}', [\App\Http\Controllers\Api\MenuAcademicoController::class, 'show'])
        ->middleware('permiso:configuracion.ver');
    Route::put('/menus-academicos/{id}', [\App\Http\Controllers\Api\MenuAcademicoController::class, 'update'])
        ->middleware('permiso:configuracion.editar');
    Route::delete('/menus-academicos/{id}', [\App\Http\Controllers\Api\MenuAcademicoController::class, 'destroy'])
        ->middleware('permiso:configuracion.eliminar');

    Route::get('/modulos-academicos', [\App\Http\Controllers\Api\ModuloAcademicoController::class, 'index'])
        ->middleware('permiso:configuracion.ver');
    Route::post('/modulos-academicos', [\App\Http\Controllers\Api\ModuloAcademicoController::class, 'store'])
        ->middleware('permiso:configuracion.crear');
    Route::get('/modulos-academicos/{id}', [\App\Http\Controllers\Api\ModuloAcademicoController::class, 'show'])
        ->middleware('permiso:configuracion.ver');
    Route::put('/modulos-academicos/{id}', [\App\Http\Controllers\Api\ModuloAcademicoController::class, 'update'])
        ->middleware('permiso:configuracion.editar');
    Route::delete('/modulos-academicos/{id}', [\App\Http\Controllers\Api\ModuloAcademicoController::class, 'destroy'])
        ->middleware('permiso:configuracion.eliminar');

    Route::get('/universidades', [\App\Http\Controllers\Api\UniversidadController::class, 'index'])
        ->middleware('permiso:estudiantes.ver');
    Route::post('/universidades', [\App\Http\Controllers\Api\UniversidadController::class, 'store'])
        ->middleware('permiso:estudiantes.crear');
    Route::get('/universidades/{id}', [\App\Http\Controllers\Api\UniversidadController::class, 'show'])
        ->middleware('permiso:estudiantes.ver');
    Route::put('/universidades/{id}', [\App\Http\Controllers\Api\UniversidadController::class, 'update'])
        ->middleware('permiso:estudiantes.editar');
    Route::delete('/universidades/{id}', [\App\Http\Controllers\Api\UniversidadController::class, 'destroy'])
        ->middleware('permiso:estudiantes.eliminar');

    Route::get('/citas-asesoria', [\App\Http\Controllers\Api\CitaAsesoriaController::class, 'index'])
        ->middleware('permiso:asesorias.ver');
    Route::post('/citas-asesoria', [\App\Http\Controllers\Api\CitaAsesoriaController::class, 'store'])
        ->middleware('permiso:asesorias.crear');
    Route::get('/citas-asesoria/{id}', [\App\Http\Controllers\Api\CitaAsesoriaController::class, 'show'])
        ->middleware('permiso:asesorias.ver');
    Route::put('/citas-asesoria/{id}', [\App\Http\Controllers\Api\CitaAsesoriaController::class, 'update'])
        ->middleware('permiso:asesorias.editar');
    Route::delete('/citas-asesoria/{id}', [\App\Http\Controllers\Api\CitaAsesoriaController::class, 'destroy'])
        ->middleware('permiso:asesorias.eliminar');

    Route::get('/categorias-noticia', [\App\Http\Controllers\Api\CategoriaNoticiaController::class, 'index'])
        ->middleware('permiso:noticias.ver');
    Route::post('/categorias-noticia', [\App\Http\Controllers\Api\CategoriaNoticiaController::class, 'store'])
        ->middleware('permiso:noticias.crear');
    Route::get('/categorias-noticia/{id}', [\App\Http\Controllers\Api\CategoriaNoticiaController::class, 'show'])
        ->middleware('permiso:noticias.ver');
    Route::put('/categorias-noticia/{id}', [\App\Http\Controllers\Api\CategoriaNoticiaController::class, 'update'])
        ->middleware('permiso:noticias.editar');
    Route::delete('/categorias-noticia/{id}', [\App\Http\Controllers\Api\CategoriaNoticiaController::class, 'destroy'])
        ->middleware('permiso:noticias.eliminar');

    Route::get('/noticias', [\App\Http\Controllers\Api\NoticiaController::class, 'index'])
        ->middleware('permiso:noticias.ver');
    Route::post('/noticias', [\App\Http\Controllers\Api\NoticiaController::class, 'store'])
        ->middleware('permiso:noticias.crear');
    Route::get('/noticias/{id}', [\App\Http\Controllers\Api\NoticiaController::class, 'show'])
        ->middleware('permiso:noticias.ver');
    Route::get('/noticias/slug/{slug}', [\App\Http\Controllers\Api\NoticiaController::class, 'showBySlug'])
        ->middleware('permiso:noticias.ver');
    Route::put('/noticias/{id}', [\App\Http\Controllers\Api\NoticiaController::class, 'update'])
        ->middleware('permiso:noticias.editar');
    Route::delete('/noticias/{id}', [\App\Http\Controllers\Api\NoticiaController::class, 'destroy'])
        ->middleware('permiso:noticias.eliminar');

    Route::get('/trivia-categorias', [\App\Http\Controllers\Api\TriviaCategoriaController::class, 'index'])
        ->middleware('permiso:trivia.ver');
    Route::post('/trivia-categorias', [\App\Http\Controllers\Api\TriviaCategoriaController::class, 'store'])
        ->middleware('permiso:trivia.crear');
    Route::get('/trivia-categorias/{id}', [\App\Http\Controllers\Api\TriviaCategoriaController::class, 'show'])
        ->middleware('permiso:trivia.ver');
    Route::put('/trivia-categorias/{id}', [\App\Http\Controllers\Api\TriviaCategoriaController::class, 'update'])
        ->middleware('permiso:trivia.editar');
    Route::delete('/trivia-categorias/{id}', [\App\Http\Controllers\Api\TriviaCategoriaController::class, 'destroy'])
        ->middleware('permiso:trivia.eliminar');

    Route::get('/trivia-niveles', [\App\Http\Controllers\Api\TriviaNivelController::class, 'index'])
        ->middleware('permiso:trivia.ver');
    Route::post('/trivia-niveles', [\App\Http\Controllers\Api\TriviaNivelController::class, 'store'])
        ->middleware('permiso:trivia.crear');
    Route::get('/trivia-niveles/{id}', [\App\Http\Controllers\Api\TriviaNivelController::class, 'show'])
        ->middleware('permiso:trivia.ver');
    Route::put('/trivia-niveles/{id}', [\App\Http\Controllers\Api\TriviaNivelController::class, 'update'])
        ->middleware('permiso:trivia.editar');
    Route::delete('/trivia-niveles/{id}', [\App\Http\Controllers\Api\TriviaNivelController::class, 'destroy'])
        ->middleware('permiso:trivia.eliminar');

    Route::get('/trivia-preguntas', [\App\Http\Controllers\Api\TriviaPreguntaController::class, 'index'])
        ->middleware('permiso:trivia.ver');
    Route::post('/trivia-preguntas', [\App\Http\Controllers\Api\TriviaPreguntaController::class, 'store'])
        ->middleware('permiso:trivia.crear');
    Route::get('/trivia-preguntas/{id}', [\App\Http\Controllers\Api\TriviaPreguntaController::class, 'show'])
        ->middleware('permiso:trivia.ver');
    Route::put('/trivia-preguntas/{id}', [\App\Http\Controllers\Api\TriviaPreguntaController::class, 'update'])
        ->middleware('permiso:trivia.editar');
    Route::delete('/trivia-preguntas/{id}', [\App\Http\Controllers\Api\TriviaPreguntaController::class, 'destroy'])
        ->middleware('permiso:trivia.eliminar');

    Route::get('/trivia-ranking', [\App\Http\Controllers\Api\TriviaRankingController::class, 'index'])
        ->middleware('permiso:trivia.ver');

    Route::get('/trivia-premios', [\App\Http\Controllers\Api\TriviaPremioController::class, 'index'])
        ->middleware('permiso:trivia.ver');
    Route::post('/trivia-premios', [\App\Http\Controllers\Api\TriviaPremioController::class, 'store'])
        ->middleware('permiso:trivia.crear');
    Route::get('/trivia-premios/{id}', [\App\Http\Controllers\Api\TriviaPremioController::class, 'show'])
        ->middleware('permiso:trivia.ver');
    Route::put('/trivia-premios/{id}', [\App\Http\Controllers\Api\TriviaPremioController::class, 'update'])
        ->middleware('permiso:trivia.editar');
    Route::delete('/trivia-premios/{id}', [\App\Http\Controllers\Api\TriviaPremioController::class, 'destroy'])
        ->middleware('permiso:trivia.eliminar');

    Route::get('/trivia-canjes', [\App\Http\Controllers\Api\TriviaCanjeController::class, 'index'])
        ->middleware('permiso:trivia.ver');
    Route::patch('/trivia-canjes/{id}/entregar', [\App\Http\Controllers\Api\TriviaCanjeController::class, 'entregar'])
        ->middleware('permiso:trivia.editar');
    Route::patch('/trivia-canjes/{id}/cancelar', [\App\Http\Controllers\Api\TriviaCanjeController::class, 'cancelar'])
        ->middleware('permiso:trivia.editar');

    Route::get('/comunicados', [\App\Http\Controllers\Api\ComunicadoController::class, 'index'])
        ->middleware('permiso:noticias.ver');
    Route::post('/comunicados', [\App\Http\Controllers\Api\ComunicadoController::class, 'store'])
        ->middleware('permiso:noticias.crear');
    Route::get('/comunicados/slug/{slug}', [\App\Http\Controllers\Api\ComunicadoController::class, 'showBySlug'])
        ->middleware('permiso:noticias.ver');
    Route::get('/comunicados/{id}', [\App\Http\Controllers\Api\ComunicadoController::class, 'show'])
        ->middleware('permiso:noticias.ver');
    Route::put('/comunicados/{id}', [\App\Http\Controllers\Api\ComunicadoController::class, 'update'])
        ->middleware('permiso:noticias.editar');
    Route::delete('/comunicados/{id}', [\App\Http\Controllers\Api\ComunicadoController::class, 'destroy'])
        ->middleware('permiso:noticias.eliminar');

    Route::get('/secretarias', [\App\Http\Controllers\Api\SecretariaController::class, 'index'])
        ->middleware('permiso:secretarias.ver');
    Route::post('/secretarias', [\App\Http\Controllers\Api\SecretariaController::class, 'store'])
        ->middleware('permiso:secretarias.crear');
    Route::get('/secretarias/slug/{slug}', [\App\Http\Controllers\Api\SecretariaController::class, 'showBySlug'])
        ->middleware('permiso:secretarias.ver');
    Route::get('/secretarias/{id}', [\App\Http\Controllers\Api\SecretariaController::class, 'show'])
        ->middleware('permiso:secretarias.ver');
    Route::put('/secretarias/{id}', [\App\Http\Controllers\Api\SecretariaController::class, 'update'])
        ->middleware('permiso:secretarias.editar');
    Route::delete('/secretarias/{id}', [\App\Http\Controllers\Api\SecretariaController::class, 'destroy'])
        ->middleware('permiso:secretarias.eliminar');

    Route::apiResource('autoridades', \App\Http\Controllers\Api\AutoridadController::class);

    Route::get('/organigrama/latest', [\App\Http\Controllers\Api\OrganigramaController::class, 'latest'])
        ->middleware('permiso:secretarias.ver');
    Route::apiResource('organigramas', \App\Http\Controllers\Api\OrganigramaController::class);

    Route::get('/tipos-norma', [\App\Http\Controllers\Api\TipoNormaController::class, 'index'])
        ->middleware('permiso:transparencia.ver');
    Route::post('/tipos-norma', [\App\Http\Controllers\Api\TipoNormaController::class, 'store'])
        ->middleware('permiso:transparencia.crear');
    Route::put('/tipos-norma/{id}', [\App\Http\Controllers\Api\TipoNormaController::class, 'update'])
        ->middleware('permiso:transparencia.editar');
    Route::delete('/tipos-norma/{id}', [\App\Http\Controllers\Api\TipoNormaController::class, 'destroy'])
        ->middleware('permiso:transparencia.eliminar');

    Route::get('/normas', [\App\Http\Controllers\Api\NormaController::class, 'index'])
        ->middleware('permiso:transparencia.ver');
    Route::post('/normas', [\App\Http\Controllers\Api\NormaController::class, 'store'])
        ->middleware('permiso:transparencia.crear');
    Route::put('/normas/{id}', [\App\Http\Controllers\Api\NormaController::class, 'update'])
        ->middleware('permiso:transparencia.editar');
    Route::delete('/normas/{id}', [\App\Http\Controllers\Api\NormaController::class, 'destroy'])
        ->middleware('permiso:transparencia.eliminar');

    Route::get('/manuales-institucionales', [\App\Http\Controllers\Api\ManualInstitucionalController::class, 'index'])
        ->middleware('permiso:transparencia.ver');
    Route::post('/manuales-institucionales', [\App\Http\Controllers\Api\ManualInstitucionalController::class, 'store'])
        ->middleware('permiso:transparencia.crear');

    Route::get('/tipos-documento-transparencia', [\App\Http\Controllers\Api\TipoDocumentoTransparenciaController::class, 'index'])
        ->middleware('permiso:transparencia.ver');
    Route::post('/tipos-documento-transparencia', [\App\Http\Controllers\Api\TipoDocumentoTransparenciaController::class, 'store'])
        ->middleware('permiso:transparencia.crear');
    Route::get('/tipos-documento-transparencia/{id}', [\App\Http\Controllers\Api\TipoDocumentoTransparenciaController::class, 'show'])
        ->middleware('permiso:transparencia.ver');
    Route::put('/tipos-documento-transparencia/{id}', [\App\Http\Controllers\Api\TipoDocumentoTransparenciaController::class, 'update'])
        ->middleware('permiso:transparencia.editar');
    Route::delete('/tipos-documento-transparencia/{id}', [\App\Http\Controllers\Api\TipoDocumentoTransparenciaController::class, 'destroy'])
        ->middleware('permiso:transparencia.eliminar');

    Route::get('/documentos-transparencia', [\App\Http\Controllers\Api\DocumentoController::class, 'index'])
        ->middleware('permiso:transparencia.ver');
    Route::post('/documentos-transparencia', [\App\Http\Controllers\Api\DocumentoController::class, 'store'])
        ->middleware('permiso:transparencia.crear');
    Route::get('/documentos-transparencia/{id}', [\App\Http\Controllers\Api\DocumentoController::class, 'show'])
        ->middleware('permiso:transparencia.ver');
    Route::put('/documentos-transparencia/{id}', [\App\Http\Controllers\Api\DocumentoController::class, 'update'])
        ->middleware('permiso:transparencia.editar');
    Route::delete('/documentos-transparencia/{id}', [\App\Http\Controllers\Api\DocumentoController::class, 'destroy'])
        ->middleware('permiso:transparencia.eliminar');

    Route::get('/directorio-institucional', [\App\Http\Controllers\Api\DirectorioInstitucionalController::class, 'index'])
        ->middleware('permiso:secretarias.ver');
    Route::post('/directorio-institucional', [\App\Http\Controllers\Api\DirectorioInstitucionalController::class, 'store'])
        ->middleware('permiso:secretarias.crear');
    Route::get('/directorio-institucional/{id}', [\App\Http\Controllers\Api\DirectorioInstitucionalController::class, 'show'])
        ->middleware('permiso:secretarias.ver');
    Route::put('/directorio-institucional/{id}', [\App\Http\Controllers\Api\DirectorioInstitucionalController::class, 'update'])
        ->middleware('permiso:secretarias.editar');
    Route::delete('/directorio-institucional/{id}', [\App\Http\Controllers\Api\DirectorioInstitucionalController::class, 'destroy'])
        ->middleware('permiso:secretarias.eliminar');

    Route::get('/historia-municipio', [\App\Http\Controllers\Api\HistoriaMunicipioController::class, 'index'])
        ->middleware('permiso:contenido.ver');
    Route::post('/historia-municipio', [\App\Http\Controllers\Api\HistoriaMunicipioController::class, 'store'])
        ->middleware('permiso:contenido.crear');
    Route::get('/historia-municipio/{id}', [\App\Http\Controllers\Api\HistoriaMunicipioController::class, 'show'])
        ->middleware('permiso:contenido.ver');
    Route::put('/historia-municipio/{id}', [\App\Http\Controllers\Api\HistoriaMunicipioController::class, 'update'])
        ->middleware('permiso:contenido.editar');
    Route::delete('/historia-municipio/{id}', [\App\Http\Controllers\Api\HistoriaMunicipioController::class, 'destroy'])
        ->middleware('permiso:contenido.eliminar');

    Route::get('/tipos-evento', [\App\Http\Controllers\Api\TipoEventoController::class, 'index'])
        ->middleware('permiso:eventos.ver');
    Route::post('/tipos-evento', [\App\Http\Controllers\Api\TipoEventoController::class, 'store'])
        ->middleware('permiso:eventos.crear');
    Route::get('/tipos-evento/{id}', [\App\Http\Controllers\Api\TipoEventoController::class, 'show'])
        ->middleware('permiso:eventos.ver');
    Route::put('/tipos-evento/{id}', [\App\Http\Controllers\Api\TipoEventoController::class, 'update'])
        ->middleware('permiso:eventos.editar');
    Route::delete('/tipos-evento/{id}', [\App\Http\Controllers\Api\TipoEventoController::class, 'destroy'])
        ->middleware('permiso:eventos.eliminar');

    Route::get('/eventos/{eventoId}/fotos', [\App\Http\Controllers\Api\EventoFotoController::class, 'index'])
        ->middleware('permiso:eventos.ver');
    Route::post('/eventos-fotos', [\App\Http\Controllers\Api\EventoFotoController::class, 'store'])
        ->middleware('permiso:eventos.crear');
    Route::get('/eventos-fotos/{id}', [\App\Http\Controllers\Api\EventoFotoController::class, 'show'])
        ->middleware('permiso:eventos.ver');
    Route::put('/eventos-fotos/{id}', [\App\Http\Controllers\Api\EventoFotoController::class, 'update'])
        ->middleware('permiso:eventos.editar');
    Route::delete('/eventos-fotos/{id}', [\App\Http\Controllers\Api\EventoFotoController::class, 'destroy'])
        ->middleware('permiso:eventos.eliminar');

    Route::get('/sugerencias-reclamos', [\App\Http\Controllers\Api\SugerenciaReclamoController::class, 'index'])
        ->middleware('permiso:sugerencias.ver');
    Route::post('/sugerencias-reclamos', [\App\Http\Controllers\Api\SugerenciaReclamoController::class, 'store'])
        ->middleware('permiso:sugerencias.crear');
    Route::get('/sugerencias-reclamos/{id}', [\App\Http\Controllers\Api\SugerenciaReclamoController::class, 'show'])
        ->middleware('permiso:sugerencias.ver');
    Route::post('/sugerencias-reclamos/{id}/responder', [\App\Http\Controllers\Api\SugerenciaReclamoController::class, 'respond'])
        ->middleware('permiso:sugerencias.editar');
    Route::delete('/sugerencias-reclamos/{id}', [\App\Http\Controllers\Api\SugerenciaReclamoController::class, 'destroy'])
        ->middleware('permiso:sugerencias.eliminar');

    
    Route::get('/cert-config-programas', [\App\Http\Controllers\Api\CertConfigProgramaController::class, 'index'])
        ->middleware('permiso:cert-config.ver');
    Route::get('/cert-config-programas/by-programa/{programaId}', [\App\Http\Controllers\Api\CertConfigProgramaController::class, 'showByPrograma'])
        ->middleware('permiso:cert-config.ver');
    Route::post('/cert-config-programas', [\App\Http\Controllers\Api\CertConfigProgramaController::class, 'upsert'])
        ->middleware('permiso:cert-config.editar');
    Route::patch('/cert-config-programas/{id}/toggle', [\App\Http\Controllers\Api\CertConfigProgramaController::class, 'toggle'])
        ->middleware('permiso:cert-config.editar');
    Route::post('/cert-config-programas/{configId}/items', [\App\Http\Controllers\Api\CertConfigProgramaController::class, 'storeItem'])
        ->middleware('permiso:cert-config.editar');
    Route::put('/cert-config-programas/{configId}/items/{itemId}', [\App\Http\Controllers\Api\CertConfigProgramaController::class, 'updateItem'])
        ->middleware('permiso:cert-config.editar');
    Route::delete('/cert-config-programas/{configId}/items/{itemId}', [\App\Http\Controllers\Api\CertConfigProgramaController::class, 'destroyItem'])
        ->middleware('permiso:cert-config.eliminar');
    Route::delete('/cert-config-programas/{id}', [\App\Http\Controllers\Api\CertConfigProgramaController::class, 'destroy'])
        ->middleware('permiso:cert-config.eliminar');

    
    Route::get('/gastos',              [\App\Http\Controllers\Api\GastoController::class, 'index'])
        ->middleware('permiso:gastos.ver');
    Route::get('/gastos/resumen-mes',  [\App\Http\Controllers\Api\GastoController::class, 'resumenMes'])
        ->middleware('permiso:gastos.ver');
    Route::get('/gastos/{gasto}',      [\App\Http\Controllers\Api\GastoController::class, 'show'])
        ->middleware('permiso:gastos.ver');
    Route::post('/gastos',             [\App\Http\Controllers\Api\GastoController::class, 'store'])
        ->middleware('permiso:gastos.crear');
    Route::match(['put', 'post'], '/gastos/{gasto}', [\App\Http\Controllers\Api\GastoController::class, 'update'])
        ->middleware('permiso:gastos.editar');
    Route::delete('/gastos/{gasto}',   [\App\Http\Controllers\Api\GastoController::class, 'destroy'])
        ->middleware('permiso:gastos.eliminar');

    Route::get('/categorias-gasto',    [\App\Http\Controllers\Api\CategoriaGastoController::class, 'index'])
        ->middleware('permiso:gastos.ver');
    Route::post('/categorias-gasto',   [\App\Http\Controllers\Api\CategoriaGastoController::class, 'store'])
        ->middleware('permiso:gastos.crear');

    Route::get('/gastos-recurrentes',              [\App\Http\Controllers\Api\GastoRecurrenteController::class, 'index'])
        ->middleware('permiso:gastos.ver');
    Route::post('/gastos-recurrentes',             [\App\Http\Controllers\Api\GastoRecurrenteController::class, 'store'])
        ->middleware('permiso:gastos.crear');
    Route::match(['patch', 'post'], '/gastos-recurrentes/{gastoRecurrente}/confirmar', [\App\Http\Controllers\Api\GastoRecurrenteController::class, 'confirmar'])
        ->middleware('permiso:gastos.crear');

    
    Route::get('/campanas-publicidad',                  [\App\Http\Controllers\Api\CampanaPublicidadController::class, 'index'])
        ->middleware('permiso:campanas.ver');
    Route::get('/campanas-publicidad/reporte',           [\App\Http\Controllers\Api\CampanaPublicidadController::class, 'reporte'])
        ->middleware('permiso:campanas.ver');
    Route::post('/campanas-publicidad/sync-meta',        [\App\Http\Controllers\Api\CampanaPublicidadController::class, 'syncMeta'])
        ->middleware('permiso:campanas.editar');
    Route::post('/campanas-publicidad/import-meta',      [\App\Http\Controllers\Api\CampanaPublicidadController::class, 'importMeta'])
        ->middleware('permiso:campanas.crear');
    Route::get('/campanas-publicidad/{id}',              [\App\Http\Controllers\Api\CampanaPublicidadController::class, 'show'])
        ->middleware('permiso:campanas.ver');
    Route::post('/campanas-publicidad',                  [\App\Http\Controllers\Api\CampanaPublicidadController::class, 'store'])
        ->middleware('permiso:campanas.crear');
    Route::match(['put', 'post'], '/campanas-publicidad/{id}', [\App\Http\Controllers\Api\CampanaPublicidadController::class, 'update'])
        ->middleware('permiso:campanas.editar');
    Route::delete('/campanas-publicidad/{id}',           [\App\Http\Controllers\Api\CampanaPublicidadController::class, 'destroy'])
        ->middleware('permiso:campanas.eliminar');
    Route::post('/campanas-publicidad/{id}/metricas',    [\App\Http\Controllers\Api\CampanaPublicidadController::class, 'registrarMetrica'])
        ->middleware('permiso:campanas.crear');


    Route::get('/campanas-leads',                    [\App\Http\Controllers\Api\CampanaLeadController::class, 'index'])
        ->middleware('permiso:leads.ver');
    Route::get('/campanas-leads/{id}',                [\App\Http\Controllers\Api\CampanaLeadController::class, 'show'])
        ->middleware('permiso:leads.ver');
    Route::post('/campanas-leads',                    [\App\Http\Controllers\Api\CampanaLeadController::class, 'store'])
        ->middleware('permiso:leads.crear');
    Route::match(['put', 'post'], '/campanas-leads/{id}', [\App\Http\Controllers\Api\CampanaLeadController::class, 'update'])
        ->middleware('permiso:leads.editar');
    Route::delete('/campanas-leads/{id}',             [\App\Http\Controllers\Api\CampanaLeadController::class, 'destroy'])
        ->middleware('permiso:leads.eliminar');

    Route::get('/campanas-leads/{campanaLeadId}/leads',              [\App\Http\Controllers\Api\LeadController::class, 'index'])
        ->middleware('permiso:leads.ver');
    Route::post('/campanas-leads/{campanaLeadId}/leads',             [\App\Http\Controllers\Api\LeadController::class, 'store'])
        ->middleware('permiso:leads.crear');
    Route::post('/campanas-leads/{campanaLeadId}/leads/importar-excel', [\App\Http\Controllers\Api\LeadController::class, 'importarExcel'])
        ->middleware('permiso:leads.crear');
    Route::match(['put', 'post'], '/campanas-leads/{campanaLeadId}/leads/{id}', [\App\Http\Controllers\Api\LeadController::class, 'update'])
        ->middleware('permiso:leads.editar');
    Route::delete('/campanas-leads/{campanaLeadId}/leads/{id}',       [\App\Http\Controllers\Api\LeadController::class, 'destroy'])
        ->middleware('permiso:leads.eliminar');

    // CRM Seguimientos de Leads
    Route::get('/campanas-leads/{campanaLeadId}/leads/{leadId}',                  [\App\Http\Controllers\Api\LeadController::class, 'show'])
        ->middleware('permiso:leads.ver');
    Route::get('/campanas-leads/{campanaLeadId}/leads/{leadId}/seguimientos',     [\App\Http\Controllers\Api\LeadSeguimientoController::class, 'index'])
        ->middleware('permiso:leads.ver');
    Route::post('/campanas-leads/{campanaLeadId}/leads/{leadId}/seguimientos',    [\App\Http\Controllers\Api\LeadSeguimientoController::class, 'store'])
        ->middleware('permiso:leads.editar');
    Route::patch('/campanas-leads/{campanaLeadId}/leads/{leadId}/estado',         [\App\Http\Controllers\Api\LeadSeguimientoController::class, 'actualizarEstado'])
        ->middleware('permiso:leads.editar');

    // Dashboard Gerencial
    Route::get('/dashboard/gerencia', [\App\Http\Controllers\Api\DashboardController::class, 'gerencia'])
        ->middleware('permiso:dashboard.ver');

    Route::get('/empleados',          [\App\Http\Controllers\Api\EmpleadoController::class, 'index'])
        ->middleware('permiso:empleados.ver');
    Route::get('/empleados/activos',  [\App\Http\Controllers\Api\EmpleadoController::class, 'activos'])
        ->middleware('permiso:empleados.ver');
    Route::get('/empleados/{empleado}', [\App\Http\Controllers\Api\EmpleadoController::class, 'show'])
        ->middleware('permiso:empleados.ver');
    Route::post('/empleados',         [\App\Http\Controllers\Api\EmpleadoController::class, 'store'])
        ->middleware('permiso:empleados.crear');
    Route::match(['put', 'post'], '/empleados/{empleado}', [\App\Http\Controllers\Api\EmpleadoController::class, 'update'])
        ->middleware('permiso:empleados.editar');
    Route::delete('/empleados/{empleado}', [\App\Http\Controllers\Api\EmpleadoController::class, 'destroy'])
        ->middleware('permiso:empleados.eliminar');

    
    Route::get('/ajustes-sueldo',    [\App\Http\Controllers\Api\AjusteSueldoController::class, 'index'])
        ->middleware('permiso:empleados.ver');
    Route::post('/ajustes-sueldo',   [\App\Http\Controllers\Api\AjusteSueldoController::class, 'store'])
        ->middleware('permiso:empleados.editar');
    Route::delete('/ajustes-sueldo/{id}', [\App\Http\Controllers\Api\AjusteSueldoController::class, 'destroy'])
        ->middleware('permiso:empleados.editar');

    
    Route::get('/planillas',          [\App\Http\Controllers\Api\PlanillaController::class, 'index'])
        ->middleware('permiso:planillas.ver');
    
    
    Route::get('/planillas/preview',  [\App\Http\Controllers\Api\PlanillaController::class, 'preview'])
        ->middleware('permiso:planillas.crear');
    Route::get('/planillas/{planilla}', [\App\Http\Controllers\Api\PlanillaController::class, 'show'])
        ->middleware('permiso:planillas.ver');
    Route::get('/planillas/{planilla}/pdf', [\App\Http\Controllers\Api\PlanillaController::class, 'pdf'])
        ->middleware('permiso:planillas.ver');
    Route::get('/planillas/{planilla}/excel', [\App\Http\Controllers\Api\PlanillaController::class, 'excel'])
        ->middleware('permiso:planillas.ver');
    Route::post('/planillas/generar', [\App\Http\Controllers\Api\PlanillaController::class, 'generar'])
        ->middleware('permiso:planillas.crear');
    Route::delete('/planillas/{planilla}', [\App\Http\Controllers\Api\PlanillaController::class, 'destroy'])
        ->middleware('permiso:planillas.eliminar');

    
    Route::get('/dashboard-gastos', [\App\Http\Controllers\Api\DashboardGastosController::class, 'index'])
        ->middleware('permiso:gastos.ver');

    
    Route::get('/config-honorarios', [\App\Http\Controllers\Api\ConfigHonorarioController::class, 'index'])
        ->middleware('permiso:honorarios.ver');
    Route::post('/config-honorarios', [\App\Http\Controllers\Api\ConfigHonorarioController::class, 'upsert'])
        ->middleware('permiso:honorarios.editar');
    Route::delete('/config-honorarios/{idPrograma}', [\App\Http\Controllers\Api\ConfigHonorarioController::class, 'destroy'])
        ->middleware('permiso:honorarios.editar');

    
    Route::get('/honorarios-sugeridos', [\App\Http\Controllers\Api\HonorarioSugeridoController::class, 'index'])
        ->middleware('permiso:honorarios.ver');

    
    Route::get('/cert-solicitudes', [\App\Http\Controllers\Api\CertSolicitudController::class, 'index'])
        ->middleware('permiso:cert-solicitudes.ver');
    Route::get('/cert-solicitudes/pendientes-count', [\App\Http\Controllers\Api\CertSolicitudController::class, 'pendientesCount'])
        ->middleware('permiso:cert-solicitudes.ver');
    Route::patch('/cert-solicitudes/{id}/aprobar', [\App\Http\Controllers\Api\CertSolicitudController::class, 'aprobar'])
        ->middleware('permiso:cert-solicitudes.editar');
    Route::patch('/cert-solicitudes/{id}/rechazar', [\App\Http\Controllers\Api\CertSolicitudController::class, 'rechazar'])
        ->middleware('permiso:cert-solicitudes.editar');
});

$rateLimitPortal = app()->environment('local') ? 'rate.portal:600,60' : 'rate.portal:60,60';

require __DIR__ . '/v1/portal.php';

require __DIR__ . '/v1/public.php';

require __DIR__ . '/v1/moodle.php';

require __DIR__ . '/v1/trivia.php';

require __DIR__ . '/v1/zoom.php';


require __DIR__ . '/v1/notificaciones.php';

require __DIR__ . '/v1/analytics.php';

require __DIR__ . '/v1/reportes.php';

require __DIR__ . '/v1/certificados_webhook.php';

require __DIR__ . '/v1/estudiante_portal.php';



require __DIR__ . '/v1/caja.php';
