<?php

use Illuminate\Support\Facades\Route;

Route::prefix('public')->middleware(['portal.key', 'encrypt.portal'])->group(function () {

    Route::get('/inscripciones-diplomado/buscar-ci', [\App\Http\Controllers\Api\InscripcionDiplomadoController::class, 'buscarCi'])
        ->middleware('throttle:60,1');
    Route::post('/inscripciones-diplomado', [\App\Http\Controllers\Api\InscripcionDiplomadoController::class, 'store'])
        ->middleware('throttle:30,1');

    Route::get('/usuarios/buscar-ci', [\App\Http\Controllers\Api\InscripcionPortalController::class, 'buscarCi'])
        ->middleware('throttle:60,1');
    Route::post('/inscripciones', [\App\Http\Controllers\Api\InscripcionPortalController::class, 'store'])
        ->middleware('throttle:30,1');
    Route::get('/captcha', [\App\Http\Controllers\Api\CaptchaController::class, 'generate'])
        ->middleware('throttle:60,1');
    Route::get('/formularios/id/{id}/publico', [\App\Http\Controllers\Api\FormularioController::class, 'showByIdPublico'])
        ->middleware('throttle:120,1');
    Route::get('/formularios/{slug}/publico', [\App\Http\Controllers\Api\FormularioController::class, 'showBySlug'])
        ->middleware('throttle:120,1');

    Route::post('/upload/file', [\App\Http\Controllers\Api\UploadController::class, 'file'])
        ->middleware('throttle:60,1');

    Route::get('/convenios', [\App\Http\Controllers\Api\ConvenioController::class, 'all']);
    Route::get('/convenios/{id}', [\App\Http\Controllers\Api\ConvenioController::class, 'showPublico']);
    Route::get('/testimonios', [\App\Http\Controllers\Api\TestimonioController::class, 'index']);
    Route::get('/areas', [\App\Http\Controllers\Api\AreaController::class, 'indexPublico']);
    Route::get('/areas/{slug}', [\App\Http\Controllers\Api\AreaController::class, 'showPublico']);
    Route::get('/cursos', [\App\Http\Controllers\Api\CursoController::class, 'index']);
    Route::get('/cursos/{id}', [\App\Http\Controllers\Api\CursoController::class, 'show']);
    Route::get('/cursos/slug/{slug}', [\App\Http\Controllers\Api\CursoController::class, 'showBySlug']);
    Route::get('/cursos/slug/{slug}/participantes', [\App\Http\Controllers\Api\CursoParticipantesController::class, 'porSlug'])
        ->middleware('throttle:60,1');
    Route::get('/categorias-programa/{categoriaId}/campos', [\App\Http\Controllers\Api\CategoriaCampoController::class, 'index']);
    Route::get('/expedido', [\App\Http\Controllers\Api\ExpedidoController::class, 'index']);
    Route::get('/grados-academicos', [\App\Http\Controllers\Api\GradoAcademicoController::class, 'indexPublico']);
    Route::get('/profesiones', [\App\Http\Controllers\Api\ProfesionController::class, 'indexPublico']);
    Route::get('/medios-pago', [\App\Http\Controllers\Api\MedioPagoController::class, 'indexPublico']);
    Route::get('/catalogo-academico/{catalogo}', [\App\Http\Controllers\Api\CatalogoAcademicoController::class, 'indexPublico']);
    Route::get('/menus/{nombre}/items', [\App\Http\Controllers\Api\WebMenuController::class, 'itemsByNombre']);
    Route::get('/efectos-especiales', [\App\Http\Controllers\Api\EfectoEspecialController::class, 'activos']);
    Route::get('/resenas', [\App\Http\Controllers\Api\ResenaController::class, 'index']);
    Route::post('/resenas', [\App\Http\Controllers\Api\ResenaController::class, 'store'])->middleware('throttle:10,1');
    Route::get('/speeches-ventas', [\App\Http\Controllers\Api\SpeechVentasController::class, 'publicIndex']);
    Route::get('/boletines', [\App\Http\Controllers\Api\BoletinController::class, 'index']);
    Route::get('/boletines/slug/{slug}', [\App\Http\Controllers\Api\BoletinController::class, 'showBySlug']);
    Route::get('/eventos', [\App\Http\Controllers\Api\EventoController::class, 'index']);
    Route::get('/fotos', [\App\Http\Controllers\Api\FotoController::class, 'index']);
    Route::get('/preguntas-frecuentes', [\App\Http\Controllers\Api\PreguntaFrecuenteController::class, 'index']);
    Route::get('/publicaciones', [\App\Http\Controllers\Api\PublicacionController::class, 'index']);
    Route::get('/publicaciones/{tipo}/slug/{slug}', [\App\Http\Controllers\Api\PublicacionController::class, 'showBySlug']);
    Route::get('/cifras-institucionales', [\App\Http\Controllers\Api\CifraInstitucionalController::class, 'indexPublico']);

    Route::get('/mis-pagos', [\App\Http\Controllers\Api\MisPagosController::class, 'index'])
        ->middleware('throttle:3,1');

    
    Route::post('/visitas', [\App\Http\Controllers\Api\VisitaController::class, 'store'])
        ->middleware('throttle:120,1');

    
    Route::get('/cert-config/{programaId}', [\App\Http\Controllers\Api\CertConfigPublicController::class, 'getConfig'])
        ->middleware('throttle:120,1');
    Route::post('/cert-solicitudes', [\App\Http\Controllers\Api\CertConfigPublicController::class, 'crearSolicitudes'])
        ->middleware('throttle:30,1');
    Route::patch('/cert-solicitudes/{id}/comprobante', [\App\Http\Controllers\Api\CertConfigPublicController::class, 'subirComprobante'])
        ->middleware('throttle:30,1');
});
