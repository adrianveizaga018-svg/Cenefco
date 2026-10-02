<?php

use App\Http\Controllers\Api\BannerPortalController;
use App\Http\Controllers\Api\MensajeContactoController;
use App\Http\Controllers\Api\RedSocialController;
use Illuminate\Support\Facades\Route;

$rateLimitPortal = app()->environment('local') ? 'rate.portal:600,60' : 'rate.portal:60,60';

Route::prefix('portal')->middleware(['portal.key', 'solo.activos', $rateLimitPortal, 'encrypt.portal'])->group(function () {

    Route::get('/banners', [BannerPortalController::class, 'index']);
    Route::get('/banners/{id}', [BannerPortalController::class, 'show']);

    Route::get('/eventos', [\App\Http\Controllers\Api\EventoController::class, 'index']);
    Route::get('/eventos/{id}', [\App\Http\Controllers\Api\EventoController::class, 'show']);

    Route::get('/redes-sociales', [RedSocialController::class, 'index']);

    Route::get('/configuracion', [\App\Http\Controllers\Api\ConfiguracionSitioController::class, 'publica']);

    Route::post('/mensajes-contacto', [MensajeContactoController::class, 'store']);

    Route::get('/preguntas-frecuentes', [\App\Http\Controllers\Api\PreguntaFrecuenteController::class, 'index']);

    Route::get('/galeria-categorias', [\App\Http\Controllers\Api\GaleriaCategoriaController::class, 'index']);
    Route::get('/galeria-categorias/{id}', [\App\Http\Controllers\Api\GaleriaCategoriaController::class, 'show']);

    Route::get('/testimonios', [\App\Http\Controllers\Api\TestimonioController::class, 'index']);
    Route::get('/testimonios/{id}', [\App\Http\Controllers\Api\TestimonioController::class, 'show']);

    Route::get('/aliados', [\App\Http\Controllers\Api\AliadoController::class, 'index']);
    Route::get('/aliados/{id}', [\App\Http\Controllers\Api\AliadoController::class, 'show']);

    Route::get('/docentes-perfil', [\App\Http\Controllers\Api\DocentePerfilController::class, 'index']);
    Route::get('/docentes-perfil/{id}', [\App\Http\Controllers\Api\DocentePerfilController::class, 'show']);

    Route::get('/acreditaciones', [\App\Http\Controllers\Api\AcreditacionController::class, 'index']);
    Route::get('/acreditaciones/{id}', [\App\Http\Controllers\Api\AcreditacionController::class, 'show']);

    Route::get('/notas-prensa', [\App\Http\Controllers\Api\NotaPrensaController::class, 'index']);
    Route::get('/notas-prensa/{id}', [\App\Http\Controllers\Api\NotaPrensaController::class, 'show']);

    Route::get('/descargables', [\App\Http\Controllers\Api\DescargableController::class, 'index']);
    Route::get('/descargables/{id}', [\App\Http\Controllers\Api\DescargableController::class, 'show']);

    Route::get('/galeria-videos', [\App\Http\Controllers\Api\GaleriaVideoController::class, 'index']);
    Route::get('/galeria-videos/{id}', [\App\Http\Controllers\Api\GaleriaVideoController::class, 'show']);

    Route::get('/calendario-academico', [\App\Http\Controllers\Api\CalendarioAcademicoController::class, 'portalIndex']);
    Route::get('/calendario-academico/cursos-vigentes', [\App\Http\Controllers\Api\CalendarioAcademicoController::class, 'vigentes']);
    Route::get('/calendario-academico/{id}', [\App\Http\Controllers\Api\CalendarioAcademicoController::class, 'portalShow']);

    Route::get('/whatsapp-grupos', [\App\Http\Controllers\Api\WhatsappGrupoController::class, 'index']);
    Route::get('/whatsapp-grupos/{id}', [\App\Http\Controllers\Api\WhatsappGrupoController::class, 'show']);

    Route::get('/certificados/codigo/{codigo}', [\App\Http\Controllers\Api\CertificadoController::class, 'showByCode']);
    Route::post('/cert-verificaciones', [\App\Http\Controllers\Api\CertVerificacionController::class, 'store']);

    Route::get('/popups', [\App\Http\Controllers\Api\PopupController::class, 'index']);

    
    Route::get('/noticias', [\App\Http\Controllers\Api\NoticiaController::class, 'index']);
    Route::get('/noticias/{id}', [\App\Http\Controllers\Api\NoticiaController::class, 'show']);
    Route::get('/noticias/slug/{slug}', [\App\Http\Controllers\Api\NoticiaController::class, 'showBySlug']);

    Route::get('/trivia/categorias', [\App\Http\Controllers\Api\TriviaCategoriaController::class, 'index']);
    Route::get('/trivia/categorias/slug/{slug}', [\App\Http\Controllers\Api\TriviaCategoriaController::class, 'showBySlug']);
    Route::get('/trivia/niveles', [\App\Http\Controllers\Api\TriviaNivelController::class, 'index']);
    Route::get('/trivia/ranking', [\App\Http\Controllers\Api\TriviaRankingController::class, 'index']);
    Route::get('/trivia/premios', [\App\Http\Controllers\Api\TriviaPremioController::class, 'indexPortal']);

    Route::get('/eventos/{eventoId}/fotos', [\App\Http\Controllers\Api\EventoFotoController::class, 'index']);

    Route::get('/comunicados', [\App\Http\Controllers\Api\ComunicadoController::class, 'index']);
    Route::get('/comunicados/slug/{slug}', [\App\Http\Controllers\Api\ComunicadoController::class, 'showBySlug']);
    Route::get('/comunicados/{id}', [\App\Http\Controllers\Api\ComunicadoController::class, 'show']);

    Route::get('/secretarias', [\App\Http\Controllers\Api\SecretariaController::class, 'index']);
    Route::get('/secretarias/{id}', [\App\Http\Controllers\Api\SecretariaController::class, 'show']);

    Route::get('/autoridades', [\App\Http\Controllers\Api\AutoridadController::class, 'index']);
    Route::get('/autoridades/{id}', [\App\Http\Controllers\Api\AutoridadController::class, 'show']);
    Route::get('/autoridades/tipo/{tipo}', [\App\Http\Controllers\Api\AutoridadController::class, 'porTipo']);

    Route::get('/normas', [\App\Http\Controllers\Api\NormaController::class, 'index']);

    Route::get('/documentos-transparencia', [\App\Http\Controllers\Api\DocumentoController::class, 'index']);
    Route::get('/documentos-transparencia/{id}', [\App\Http\Controllers\Api\DocumentoController::class, 'show']);

    Route::get('/categorias-noticia', [\App\Http\Controllers\Api\CategoriaNoticiaController::class, 'index']);
    Route::get('/tipos-evento', [\App\Http\Controllers\Api\TipoEventoController::class, 'index']);

    Route::get('/articulos', [\App\Http\Controllers\Api\ArticuloController::class, 'index']);
    Route::get('/articulos/slug/{slug}', [\App\Http\Controllers\Api\ArticuloController::class, 'showBySlug']);
    Route::get('/articulos/{id}', [\App\Http\Controllers\Api\ArticuloController::class, 'show']);

    Route::get('/cursos-pasados', [\App\Http\Controllers\Api\CursosPasadosController::class, 'index']);
    Route::get('/cursos-pasados/{slug}', [\App\Http\Controllers\Api\CursosPasadosController::class, 'show']);

    Route::get('/hitos-institucionales', [\App\Http\Controllers\Api\HitoInstitucionalController::class, 'index']);
});
