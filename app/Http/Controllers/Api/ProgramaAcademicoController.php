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
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteProgramaAcademicoCommand($id));

        return response()->json(null, 204);
    }
}
