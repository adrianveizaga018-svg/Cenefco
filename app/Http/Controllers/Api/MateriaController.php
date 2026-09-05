<?php

namespace App\Http\Controllers\Api;

use App\Application\Materias\Commands\CreateMateriaCommand;
use App\Application\Materias\Commands\DeleteMateriaCommand;
use App\Application\Materias\Commands\UpdateMateriaCommand;
use App\Application\Materias\Handlers\CreateMateriaHandler;
use App\Application\Materias\Handlers\DeleteMateriaHandler;
use App\Application\Materias\Handlers\UpdateMateriaHandler;
use App\Application\Materias\Queries\GetMateriaByIdQuery;
use App\Application\Materias\Queries\GetMateriasQuery;
use App\Application\Materias\QueryHandlers\GetMateriaByIdQueryHandler;
use App\Application\Materias\QueryHandlers\GetMateriasQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Materias\StoreMateriaRequest;
use App\Http\Requests\Materias\UpdateMateriaRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MateriaController extends Controller
{
    public function __construct(
        private readonly GetMateriasQueryHandler $getMateriasHandler,
        private readonly GetMateriaByIdQueryHandler $getMateriaByIdHandler,
        private readonly CreateMateriaHandler $createHandler,
        private readonly UpdateMateriaHandler $updateHandler,
        private readonly DeleteMateriaHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 50),
            'query'     => $request->get('query', ''),
            'sortKey'   => 'nombre',
            'sortOrder' => 'asc',
        ]);

        return response()->json(
            $this->getMateriasHandler->handle(new GetMateriasQuery(
                pagination:   $pagination,
                conInactivos: $request->boolean('conInactivos', false),
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getMateriaByIdHandler->handle(new GetMateriaByIdQuery($id))
        );
    }

    public function store(StoreMateriaRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateMateriaCommand(
            id_mat:        $request->integer('id_mat'),
            id_us_reg:     $request->integer('id_us_reg', 0),
            sigla:         $request->sigla,
            nombremat:     $request->nombremat,
            nombre:        $request->nombre,
            semestre:      $request->semestre,
            modalidad:     $request->integer('modalidad'),
            carga_horaria: $request->carga_horaria,
            observacion:   $request->observacion,
            estado:        $request->integer('estado', 1),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateMateriaRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateMateriaCommand(
            id:            $id,
            sigla:         $request->sigla,
            nombremat:     $request->nombremat,
            nombre:        $request->nombre,
            semestre:      $request->semestre,
            modalidad:     $request->filled('modalidad') ? $request->integer('modalidad') : null,
            carga_horaria: $request->carga_horaria,
            observacion:   $request->observacion,
            estado:        $request->filled('estado') ? $request->integer('estado') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteMateriaCommand($id));

        return response()->json(null, 204);
    }
}
