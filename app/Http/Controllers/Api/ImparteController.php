<?php

namespace App\Http\Controllers\Api;

use App\Application\Imparticiones\Commands\CreateImparteCommand;
use App\Application\Imparticiones\Commands\DeleteImparteCommand;
use App\Application\Imparticiones\Commands\UpdateImparteCommand;
use App\Application\Imparticiones\Handlers\CreateImparteHandler;
use App\Application\Imparticiones\Handlers\DeleteImparteHandler;
use App\Application\Imparticiones\Handlers\UpdateImparteHandler;
use App\Application\Imparticiones\Queries\GetImparteByIdQuery;
use App\Application\Imparticiones\Queries\GetImparticionesQuery;
use App\Application\Imparticiones\QueryHandlers\GetImparteByIdQueryHandler;
use App\Application\Imparticiones\QueryHandlers\GetImparticionesQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Imparticiones\StoreImparteRequest;
use App\Http\Requests\Imparticiones\UpdateImparteRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImparteController extends Controller
{
    public function __construct(
        private readonly GetImparticionesQueryHandler $getImparticionesHandler,
        private readonly GetImparteByIdQueryHandler   $getImparteByIdHandler,
        private readonly CreateImparteHandler          $createHandler,
        private readonly UpdateImparteHandler          $updateHandler,
        private readonly DeleteImparteHandler          $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 30),
            'query'     => $request->get('query', ''),
            'sortKey'   => $request->input('sort.key', 'id_imp'),
            'sortOrder' => $request->input('sort.order', 'desc'),
        ]);

        return response()->json(
            $this->getImparticionesHandler->handle(new GetImparticionesQuery(
                pagination:  $pagination,
                conInactivos: $request->boolean('conInactivos', false),
                periodo:     $request->filled('periodo') ? $request->get('periodo') : null,
                gestion:     $request->filled('gestion') ? $request->get('gestion') : null,
                idMat:       $request->filled('id_mat') ? (int) $request->get('id_mat') : null,
                idUs:        $request->filled('id_us') ? (int) $request->get('id_us') : null,
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getImparteByIdHandler->handle(new GetImparteByIdQuery($id))
        );
    }

    public function store(StoreImparteRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateImparteCommand(
            id_imp:             $request->integer('id_imp'),
            id_us_reg:          $request->filled('id_us_reg') ? $request->integer('id_us_reg') : null,
            num_imp:            $request->filled('num_imp') ? $request->integer('num_imp') : null,
            periodo:            $request->input('periodo'),
            gestion:            $request->input('gestion'),
            id_us:              $request->filled('id_us') ? $request->integer('id_us') : null,
            id_mat:             $request->filled('id_mat') ? $request->integer('id_mat') : null,
            paralelo:           $request->input('paralelo'),
            cupo:               $request->input('cupo'),
            observacion_imp:    $request->input('observacion_imp'),
            nro_resolucion_hcu: $request->input('nro_resolucion_hcu'),
            id_moodle:          $request->filled('id_moodle') ? $request->integer('id_moodle') : null,
            estado:             $request->integer('estado', 1),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateImparteRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateImparteCommand(
            id:                 $id,
            periodo:            $request->input('periodo'),
            gestion:            $request->input('gestion'),
            id_us:              $request->filled('id_us') ? $request->integer('id_us') : null,
            id_mat:             $request->filled('id_mat') ? $request->integer('id_mat') : null,
            paralelo:           $request->input('paralelo'),
            cupo:               $request->input('cupo'),
            observacion_imp:    $request->input('observacion_imp'),
            nro_resolucion_hcu: $request->input('nro_resolucion_hcu'),
            estado:             $request->filled('estado') ? $request->integer('estado') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteImparteCommand($id));

        return response()->json(null, 204);
    }
}
