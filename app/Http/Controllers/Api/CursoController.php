<?php

namespace App\Http\Controllers\Api;

use App\Application\Cursos\Commands\AttachDocenteCommand;
use App\Application\Cursos\Commands\CreateCursoCommand;
use App\Application\Cursos\Commands\DeleteCursoCommand;
use App\Application\Cursos\Commands\DetachDocenteCommand;
use App\Application\Cursos\Commands\ObtenerOCrearPlanCobrosCommand;
use App\Application\Cursos\Commands\UpdateCursoCommand;
use App\Application\Cursos\Handlers\AttachDocenteHandler;
use App\Application\Cursos\Handlers\CreateCursoHandler;
use App\Application\Cursos\Handlers\DeleteCursoHandler;
use App\Application\Cursos\Handlers\DetachDocenteHandler;
use App\Application\Cursos\Handlers\ObtenerOCrearPlanCobrosHandler;
use App\Application\Cursos\Handlers\UpdateCursoHandler;
use App\Application\Cursos\Queries\GetAlertasCobrosQuery;
use App\Application\Cursos\Queries\GetCursoByIdQuery;
use App\Application\Cursos\Queries\GetCursoBySlugQuery;
use App\Application\Cursos\Queries\GetCursoMoodleExportQuery;
use App\Application\Cursos\Queries\GetCursosEstadisticasDetalleQuery;
use App\Application\Cursos\Queries\GetCursosEstadisticasQuery;
use App\Application\Cursos\Queries\GetCursosQuery;
use App\Application\Cursos\Queries\GetDocentesByCursoQuery;
use App\Application\Cursos\Queries\GetReporteEnviosDocumentosQuery;
use App\Application\Cursos\QueryHandlers\GetAlertasCobrosQueryHandler;
use App\Application\Cursos\QueryHandlers\GetCursoByIdQueryHandler;
use App\Application\Cursos\QueryHandlers\GetCursoBySlugQueryHandler;
use App\Application\Cursos\QueryHandlers\GetCursoMoodleExportQueryHandler;
use App\Application\Cursos\QueryHandlers\GetCursosEstadisticasDetalleQueryHandler;
use App\Application\Cursos\QueryHandlers\GetCursosEstadisticasQueryHandler;
use App\Application\Cursos\QueryHandlers\GetCursosQueryHandler;
use App\Application\Cursos\QueryHandlers\GetDocentesByCursoQueryHandler;
use App\Application\Cursos\QueryHandlers\GetReporteEnviosDocumentosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cursos\StoreCursoRequest;
use App\Http\Requests\Cursos\UpdateCursoRequest;
use App\Infrastructure\Vendedores\Services\VendedorScopeResolver;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CursoController extends Controller
{
    public function __construct(
        private readonly GetCursosQueryHandler $getCursosHandler,
        private readonly GetCursoByIdQueryHandler $getByIdHandler,
        private readonly GetCursoBySlugQueryHandler $getBySlugHandler,
        private readonly GetDocentesByCursoQueryHandler $getDocentesHandler,
        private readonly GetCursosEstadisticasQueryHandler $getEstadisticasHandler,
        private readonly GetCursosEstadisticasDetalleQueryHandler $getEstadisticasDetalleHandler,
        private readonly GetReporteEnviosDocumentosQueryHandler $getReporteEnviosDocumentosHandler,
        private readonly GetCursoMoodleExportQueryHandler $getMoodleExportHandler,
        private readonly CreateCursoHandler $createHandler,
        private readonly UpdateCursoHandler $updateHandler,
        private readonly DeleteCursoHandler $deleteHandler,
        private readonly AttachDocenteHandler $attachDocenteHandler,
        private readonly DetachDocenteHandler $detachDocenteHandler,
        private readonly ObtenerOCrearPlanCobrosHandler $obtenerOCrearPlanCobrosHandler,
        private readonly GetAlertasCobrosQueryHandler $getAlertasCobrosHandler,
        private readonly VendedorScopeResolver $vendedorScope,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 15),
            'query'     => $request->get('query', ''),
            'sortKey'   => 'orden',
            'sortOrder' => 'asc',
        ]);

        $idsPermitidos = auth()->check() ? $this->vendedorScope->programaIdsPermitidos(auth()->user()) : null;

        return response()->json($this->getCursosHandler->handle(
            new GetCursosQuery($pagination, soloPublicados: ! auth()->check(), idsPermitidos: $idsPermitidos)
        ));
    }

    public function estadisticas(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'periodo'   => ['nullable', 'in:dia,mes,anio,rango'],
            'fecha'     => ['nullable', 'date'],
            'fecha_fin' => ['required_if:periodo,rango', 'nullable', 'date', 'after_or_equal:fecha'],
        ]);

        $dto = $this->getEstadisticasHandler->handle(new GetCursosEstadisticasQuery(
            periodo: $validated['periodo'] ?? 'mes',
            fecha:   $validated['fecha'] ?? now()->toDateString(),
            fechaFin: $validated['fecha_fin'] ?? null,
            idImpPermitidos: $this->vendedorScope->idImpPermitidos(auth()->user()),
        ));

        return response()->json($dto);
    }

    public function estadisticasDetalle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'periodo'   => ['nullable', 'in:dia,mes,anio,rango'],
            'fecha'     => ['nullable', 'date'],
            'fecha_fin' => ['required_if:periodo,rango', 'nullable', 'date', 'after_or_equal:fecha'],
        ]);

        $dto = $this->getEstadisticasDetalleHandler->handle(new GetCursosEstadisticasDetalleQuery(
            periodo: $validated['periodo'] ?? 'mes',
            fecha:   $validated['fecha'] ?? now()->toDateString(),
            fechaFin: $validated['fecha_fin'] ?? null,
            idImpPermitidos: $this->vendedorScope->idImpPermitidos(auth()->user()),
        ));

        return response()->json($dto);
    }

    public function planCobros(int $id): JsonResponse
    {
        $this->vendedorScope->assertAccesoPrograma(auth()->user(), $id);

        try {
            $dto = $this->obtenerOCrearPlanCobrosHandler->handle(new ObtenerOCrearPlanCobrosCommand(
                idPrograma: $id,
                idUsReg:    auth()->id() ?? 0,
            ));

            return response()->json($dto, $dto->creado ? 201 : 200);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 422);
        }
    }

    public function alertasCobros(Request $request): JsonResponse
    {
        $dias = $request->filled('dias') ? (int) $request->integer('dias') : 3;

        $dtos = $this->getAlertasCobrosHandler->handle(new GetAlertasCobrosQuery(
            diasProximos:    $dias,
            idImpPermitidos: $this->vendedorScope->idImpPermitidos(auth()->user()),
        ));

        return response()->json($dtos);
    }

    public function reporteEnviosDocumentos(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin'    => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
        ]);

        $dto = $this->getReporteEnviosDocumentosHandler->handle(new GetReporteEnviosDocumentosQuery(
            fechaInicio: $validated['fecha_inicio'] ?? null,
            fechaFin:    $validated['fecha_fin'] ?? null,
            idImpPermitidos: $this->vendedorScope->idImpPermitidos(auth()->user()),
        ));

        return response()->json($dto);
    }

    public function show(int $id): JsonResponse
    {
        $dto = $this->getByIdHandler->handle(new GetCursoByIdQuery($id, soloPublicados: ! auth()->check()));
        $this->verificarAccesoVendedor($dto->id_programa);

        return response()->json($dto);
    }

    public function showBySlug(string $slug): JsonResponse
    {
        $dto = $this->getBySlugHandler->handle(
            new GetCursoBySlugQuery($slug, soloPublicados: ! auth()->check())
        );
        $this->verificarAccesoVendedor($dto->id_programa);

        return response()->json($dto);
    }

    
    
    
    
    private function verificarAccesoVendedor(int $idPrograma): void
    {
        if (! auth()->check()) {
            return;
        }

        $this->vendedorScope->assertAccesoPrograma(auth()->user(), $idPrograma);
    }

    
    
    
    
    public function moodleExport(Request $request): StreamedResponse
    {
        $request->validate([
            'id_imp' => ['required', 'integer'],
        ]);

        $this->vendedorScope->assertAccesoImparte(auth()->user(), (int) $request->id_imp);

        $estudiantes = $this->getMoodleExportHandler->handle(new GetCursoMoodleExportQuery(
            idImp: (int) $request->id_imp,
        ));

        $filename = 'moodle-usuarios-imp'.$request->id_imp.'.csv';

        return response()->stream(function () use ($estudiantes) {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['username', 'password', 'firstname', 'lastname', 'email']);

            foreach ($estudiantes as $e) {
                fputcsv($out, [$e->username, $e->password, $e->firstname, $e->lastname, $e->email]);
            }

            fclose($out);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function store(StoreCursoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateCursoCommand(
            nombre_programa:          $request->nombre_programa,
            slug:                     $request->slug,
            descripcion:              $request->descripcion,
            objetivo:                 $request->objetivo,
            dirigido:                 $request->dirigido,
            requisitos:               $request->requisitos,
            inversion:                $request->inversion,
            costo_monto:              $request->filled('costo_monto') ? (float) $request->costo_monto : null,
            creditaje:                $request->creditaje,
            nota:                     $request->nota,
            foto:                     $request->foto,
            titulo_documento1:        $request->titulo_documento1,
            documento1:               $request->documento1,
            imagen_banner_url:        $request->imagen_banner_url,
            imagen_alt:               $request->imagen_alt,
            url_video:                $request->url_video,
            url_whatsapp:             $request->url_whatsapp,
            url_whatsapp2:            $request->url_whatsapp2,
            imagenes:                 $request->imagenes,
            inicio_actividades:       $request->inicio_actividades,
            finalizacion_actividades: $request->finalizacion_actividades,
            inicio_inscripciones:     $request->inicio_inscripciones,
            tipo_honorario:           $request->tipo_honorario,
            id_tipoprograma:          $request->id_tipoprograma ? (int) $request->id_tipoprograma : null,
            categoria_web_id:         $request->categoria_web_id ? (int) $request->categoria_web_id : null,
            formulario_id:              $request->filled('formulario_id') ? (int) $request->formulario_id : null,
            area_id:                  $request->area_id ? (int) $request->area_id : null,
            estado_web:               $request->input('estado_web', 'borrador'),
            destacado:                $request->boolean('destacado', false),
            orden:                    $request->integer('orden', 0),
            meta_titulo:              $request->meta_titulo,
            meta_descripcion:         $request->meta_descripcion,
            mensaje_exito:            $request->mensaje_exito,
            id_plan:                  $request->id_plan ? (int) $request->id_plan : null,
            id_plandoc:               $request->id_plandoc ? (int) $request->id_plandoc : null,
            id_imp:                   $request->id_imp ? (int) $request->id_imp : null,
            convenio_id:              $request->convenio_id ? (int) $request->convenio_id : null,
            vendedor_id:              $request->vendedor_id ? (int) $request->vendedor_id : null,
            tareas_catalogo_ids:      $request->tareas_catalogo_ids,
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateCursoRequest $request, int $id): JsonResponse
    {
        $this->vendedorScope->assertAccesoPrograma(auth()->user(), $id);

        return response()->json($this->updateHandler->handle(new UpdateCursoCommand(
            id:                       $id,
            nombre_programa:          $request->nombre_programa,
            slug:                     $request->slug,
            descripcion:              $request->descripcion,
            objetivo:                 $request->objetivo,
            dirigido:                 $request->dirigido,
            requisitos:               $request->requisitos,
            inversion:                $request->inversion,
            costo_monto:              $request->filled('costo_monto') ? (float) $request->costo_monto : null,
            creditaje:                $request->creditaje,
            nota:                     $request->nota,
            foto:                     $request->foto,
            titulo_documento1:        $request->titulo_documento1,
            documento1:               $request->documento1,
            imagen_banner_url:        $request->imagen_banner_url,
            imagen_alt:               $request->imagen_alt,
            url_video:                $request->url_video,
            url_whatsapp:             $request->url_whatsapp,
            url_whatsapp2:            $request->url_whatsapp2,
            imagenes:                 $request->has('imagenes') ? ($request->imagenes ?? []) : null,
            inicio_actividades:       $request->inicio_actividades,
            finalizacion_actividades: $request->finalizacion_actividades,
            inicio_inscripciones:     $request->inicio_inscripciones,
            mes_facturacion:          $request->mes_facturacion,
            tipo_honorario:           $request->tipo_honorario,
            id_tipoprograma:          $request->filled('id_tipoprograma') ? (int) $request->id_tipoprograma : null,
            categoria_web_id:         $request->filled('categoria_web_id') ? (int) $request->categoria_web_id : null,
            formulario_id:              $request->filled('formulario_id') ? (int) $request->formulario_id : null,
            area_id:                  $request->filled('area_id') ? (int) $request->area_id : null,
            estado_web:               $request->estado_web,
            destacado:                $request->has('destacado') ? $request->boolean('destacado') : null,
            orden:                    $request->has('orden') ? $request->integer('orden') : null,
            meta_titulo:              $request->meta_titulo,
            meta_descripcion:         $request->meta_descripcion,
            mensaje_exito:            $request->mensaje_exito,
            id_plan:                  $request->filled('id_plan') ? (int) $request->id_plan : null,
            id_plandoc:               $request->filled('id_plandoc') ? (int) $request->id_plandoc : null,
            id_imp:                   $request->filled('id_imp') ? (int) $request->id_imp : null,
            convenio_id:              $request->filled('convenio_id') ? (int) $request->convenio_id : null,
            vendedor_id:              $request->filled('vendedor_id') ? (int) $request->vendedor_id : null,
            tareas_catalogo_ids:      $request->tareas_catalogo_ids,
        )));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->vendedorScope->assertAccesoPrograma(auth()->user(), $id);

        $this->deleteHandler->handle(new DeleteCursoCommand($id));

        return response()->json(null, 204);
    }

    public function docentesIndex(Request $request, int $id): JsonResponse
    {
        $this->vendedorScope->assertAccesoPrograma(auth()->user(), $id);

        return response()->json($this->getDocentesHandler->handle(new GetDocentesByCursoQuery(
            programa_id:   $id,
            soloVisibles:  $request->boolean('soloVisibles', false),
        )));
    }

    public function docentesAttach(Request $request, int $id): JsonResponse
    {
        $this->vendedorScope->assertAccesoPrograma(auth()->user(), $id);

        $validated = $request->validate([
            'docente_id' => ['required', 'integer', 'exists:web_docente_perfil,id'],
            'orden'      => ['nullable', 'integer'],
        ]);

        $this->attachDocenteHandler->handle(new AttachDocenteCommand(
            programa_id: $id,
            docente_id:  (int) $validated['docente_id'],
            orden:       (int) ($validated['orden'] ?? 0),
        ));

        return response()->json(null, 201);
    }

    public function docentesDetach(int $id, int $docenteId): JsonResponse
    {
        $this->vendedorScope->assertAccesoPrograma(auth()->user(), $id);

        $this->detachDocenteHandler->handle(new DetachDocenteCommand(
            programa_id: $id,
            docente_id:  $docenteId,
        ));

        return response()->json(null, 204);
    }
}
