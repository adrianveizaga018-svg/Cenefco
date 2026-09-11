<?php

namespace App\Http\Controllers\Api;

use App\Application\ProgramasAcademicos\Commands\CreateProgramaAcademicoCommand;
use App\Application\ProgramasAcademicos\Commands\DeleteProgramaAcademicoCommand;
use App\Application\ProgramasAcademicos\Commands\UpdateProgramaAcademicoCommand;
use App\Application\ProgramasAcademicos\Handlers\CreateProgramaAcademicoHandler;
use App\Application\ProgramasAcademicos\Handlers\DeleteProgramaAcademicoHandler;
use App\Application\ProgramasAcademicos\Handlers\UpdateProgramaAcademicoHandler;
use App\Application\ProgramasAcademicos\Queries\GetProgramaAcademicoByIdQuery;
use App\Application\ProgramasAcademicos\Queries\GetProgramasAcademicosQuery;
use App\Application\ProgramasAcademicos\QueryHandlers\GetProgramaAcademicoByIdQueryHandler;
use App\Application\ProgramasAcademicos\QueryHandlers\GetProgramasAcademicosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProgramasAcademicos\StoreProgramaAcademicoRequest;
use App\Http\Requests\ProgramasAcademicos\UpdateProgramaAcademicoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgramaAcademicoController extends Controller
{
    public function __construct(
        private readonly GetProgramasAcademicosQueryHandler  $getProgramasHandler,
        private readonly GetProgramaAcademicoByIdQueryHandler $getProgramaByIdHandler,
        private readonly CreateProgramaAcademicoHandler       $createHandler,
        private readonly UpdateProgramaAcademicoHandler       $updateHandler,
        private readonly DeleteProgramaAcademicoHandler       $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 30),
            'query'     => $request->get('query', ''),
            'sortKey'   => $request->input('sort.key', 'nombre_programa'),
            'sortOrder' => $request->input('sort.order', 'asc'),
        ]);

        return response()->json(
            $this->getProgramasHandler->handle(new GetProgramasAcademicosQuery(
                pagination:      $pagination,
                conInactivos:    $request->boolean('conInactivos', false),
                idTipoprograma:  $request->filled('id_tipoprograma') ? (int) $request->get('id_tipoprograma') : null,
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getProgramaByIdHandler->handle(new GetProgramaAcademicoByIdQuery($id))
        );
    }

    public function store(StoreProgramaAcademicoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateProgramaAcademicoCommand(
            id_programa:              $request->integer('id_programa'),
            id_us_reg:                $request->filled('id_us_reg') ? $request->integer('id_us_reg') : null,
            num_programa:             $request->filled('num_programa') ? $request->integer('num_programa') : null,
            nombre_programa:          $request->input('nombre_programa'),
            descripcion:              $request->input('descripcion'),
            foto:                     $request->input('foto'),
            inicio_actividades:       $request->input('inicio_actividades'),
            finalizacion_actividades: $request->input('finalizacion_actividades'),
            inicio_inscripciones:     $request->input('inicio_inscripciones'),
            titulo_documento1:        $request->input('titulo_documento1'),
            documento1:               $request->input('documento1'),
            titulo_documento2:        $request->input('titulo_documento2'),
            documento2:               $request->input('documento2'),
            titulo_documento3:        $request->input('titulo_documento3'),
            documento3:               $request->input('documento3'),
            titulo_documento4:        $request->input('titulo_documento4'),
            documento4:               $request->input('documento4'),
            dirigido:                 $request->input('dirigido'),
            inversion:                $request->input('inversion'),
            requisitos:               $request->input('requisitos'),
            creditaje:                $request->input('creditaje'),
            objetivo:                 $request->input('objetivo'),
            nota:                     $request->input('nota'),
            id_tipoprograma:          $request->filled('id_tipoprograma') ? $request->integer('id_tipoprograma') : null,
            url_video:                $request->input('url_video'),
            estado:                   $request->integer('estado', 1),
            estado_web:               $request->input('estado_web', 'borrador'),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateProgramaAcademicoRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateProgramaAcademicoCommand(
            id:                       $id,
            nombre_programa:          $request->input('nombre_programa'),
            descripcion:              $request->input('descripcion'),
            foto:                     $request->input('foto'),
            inicio_actividades:       $request->input('inicio_actividades'),
            finalizacion_actividades: $request->input('finalizacion_actividades'),
            inicio_inscripciones:     $request->input('inicio_inscripciones'),
            titulo_documento1:        $request->input('titulo_documento1'),
            documento1:               $request->input('documento1'),
            titulo_documento2:        $request->input('titulo_documento2'),
            documento2:               $request->input('documento2'),
            titulo_documento3:        $request->input('titulo_documento3'),
            documento3:               $request->input('documento3'),
            titulo_documento4:        $request->input('titulo_documento4'),
            documento4:               $request->input('documento4'),
            dirigido:                 $request->input('dirigido'),
            inversion:                $request->input('inversion'),
            requisitos:               $request->input('requisitos'),
            creditaje:                $request->input('creditaje'),
            objetivo:                 $request->input('objetivo'),
            nota:                     $request->input('nota'),
            id_tipoprograma:          $request->filled('id_tipoprograma') ? $request->integer('id_tipoprograma') : null,
            url_video:                $request->input('url_video'),
            estado:                   $request->filled('estado') ? $request->integer('estado') : null,
            estado_web:               $request->input('estado_web'),
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        // Protección: no eliminar si tiene imparticiones o inscripciones históricas
        $tieneImparticiones = DB::table('t_imparte')->where('id_mat', $id)->exists();
        $tieneInscripciones = DB::table('t_inscripcion')
            ->join('t_imparte', 't_inscripcion.id_imp', '=', 't_imparte.id_imp')
            ->where('t_imparte.id_mat', $id)
            ->exists();

        if ($tieneImparticiones || $tieneInscripciones) {
            return response()->json([
                'message' => 'No se puede eliminar este programa porque tiene imparticiones o inscripciones registradas. Usa "Desactivado" para ocultarlo.'
            ], 422);
        }

        $this->deleteHandler->handle(new DeleteProgramaAcademicoCommand($id));

        return response()->json(null, 204);
    }

    // ── Imparticiones (versiones) ─────────────────────────────────────────────

    public function imparticiones(int $id): JsonResponse
    {
        $rows = DB::table('t_imparte')
            ->where('id_mat', $id)
            ->orderByDesc('id_imp')
            ->select('id_imp', 'nombre', 'periodo', 'gestion', 'imparte_fecha_inicio', 'imparte_fecha_fin', 'estado')
            ->get();
        return response()->json($rows);
    }

    public function storeImparticion(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'nombre'               => 'required|string|max:200',
            'periodo'              => 'nullable|string|max:50',
            'gestion'              => 'nullable|integer',
            'imparte_fecha_inicio' => 'nullable|date',
            'imparte_fecha_fin'    => 'nullable|date',
        ]);

        $idImp = (DB::table('t_imparte')->max('id_imp') ?? 0) + 1;

        DB::table('t_imparte')->insert([
            'id_imp'               => $idImp,
            'id_mat'               => $id,
            'nombre'               => $data['nombre'],
            'periodo'              => $data['periodo'] ?? null,
            'gestion'              => $data['gestion'] ?? now()->year,
            'imparte_fecha_inicio' => $data['imparte_fecha_inicio'] ?? null,
            'imparte_fecha_fin'    => $data['imparte_fecha_fin'] ?? null,
            'estado'               => 1,
        ]);

        return response()->json(['id_imp' => $idImp, 'id_mat' => $id] + $data, 201);
    }

    public function updateImparticion(Request $request, int $id, int $id_imp): JsonResponse
    {
        $data = $request->validate([
            'nombre'               => 'required|string|max:200',
            'periodo'              => 'nullable|string|max:50',
            'gestion'              => 'nullable|integer',
            'imparte_fecha_inicio' => 'nullable|date',
            'imparte_fecha_fin'    => 'nullable|date',
            'estado'               => 'nullable|integer',
        ]);

        DB::table('t_imparte')
            ->where('id_imp', $id_imp)
            ->where('id_mat', $id)
            ->update($data);

        return response()->json(['id_imp' => $id_imp] + $data);
    }

    // ── Planes habilitados para un programa ────────────────────────────────────

    public function planes(int $id): JsonResponse
    {
        $planes = DB::table('t_plan as p')
            ->join('programa_planes as pp', 'pp.id_plan', '=', 'p.id_plan')
            ->where('pp.id_programa', $id)
            ->select('p.id_plan', 'p.titulo', 'p.costo', 'p.nro_cuotas', 'p.descuento', 'p.estado')
            ->get();

        $todosLosPlanes = DB::table('t_plan')
            ->where('estado', 1)
            ->select('id_plan', 'titulo', 'costo', 'nro_cuotas')
            ->get();

        return response()->json([
            'planes_habilitados' => $planes,
            'todos_los_planes'   => $todosLosPlanes,
        ]);
    }

    public function syncPlanes(Request $request, int $id): JsonResponse
    {
        $request->validate(['planes' => 'required|array', 'planes.*' => 'integer']);

        // Eliminar planes actuales y reemplazar
        DB::table('programa_planes')->where('id_programa', $id)->delete();

        $rows = array_map(fn($planId) => [
            'id_programa'  => $id,
            'id_plan'      => $planId,
            'created_at'   => now(),
            'updated_at'   => now(),
        ], $request->input('planes'));

        if (!empty($rows)) {
            DB::table('programa_planes')->insert($rows);
        }

        return response()->json(['planes_count' => count($rows)]);
    }
}
