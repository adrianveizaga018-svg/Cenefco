<?php

namespace App\Http\Controllers\Api;

use App\Application\Notas\Commands\CreateNotaCommand;
use App\Application\Notas\Commands\DeleteNotaCommand;
use App\Application\Notas\Commands\UpdateNotaCommand;
use App\Application\Notas\Handlers\CreateNotaHandler;
use App\Application\Notas\Handlers\DeleteNotaHandler;
use App\Application\Notas\Handlers\UpdateNotaHandler;
use App\Application\Notas\Queries\GetNotaByIdQuery;
use App\Application\Notas\Queries\GetNotasQuery;
use App\Application\Notas\QueryHandlers\GetNotaByIdQueryHandler;
use App\Application\Notas\QueryHandlers\GetNotasQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Notas\StoreNotaRequest;
use App\Http\Requests\Notas\UpdateNotaRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotaController extends Controller
{
    public function __construct(
        private readonly GetNotasQueryHandler $getNotasHandler,
        private readonly GetNotaByIdQueryHandler $getNotaByIdHandler,
        private readonly CreateNotaHandler $createHandler,
        private readonly UpdateNotaHandler $updateHandler,
        private readonly DeleteNotaHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 50),
            'query'     => '',
            'sortKey'   => 'id_not',
            'sortOrder' => 'desc',
        ]);

        return response()->json(
            $this->getNotasHandler->handle(new GetNotasQuery(
                pagination:   $pagination,
                conInactivos: $request->boolean('conInactivos', false),
                idUs:         $request->has('id_us') ? $request->integer('id_us') : null,
                idImp:        $request->has('id_imp') ? $request->integer('id_imp') : null,
                idMat:        $request->has('id_mat') ? $request->integer('id_mat') : null,
                periodo:      $request->get('periodo'),
                gestion:      $request->get('gestion'),
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->getNotaByIdHandler->handle(new GetNotaByIdQuery($id))
        );
    }

    public function store(StoreNotaRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateNotaCommand(
            id_not:    $request->integer('id_not'),
            id_us_reg: $request->integer('id_us_reg', 0),
            periodo:   $request->periodo,
            gestion:   $request->gestion,
            id_imp:    $request->integer('id_imp'),
            id_us:     $request->integer('id_us'),
            id_mat:    $request->filled('id_mat') ? $request->integer('id_mat') : null,
            nota:      $request->integer('nota'),
            nota_seg:  $request->integer('nota_seg', 0),
            paralelo:  $request->paralelo,
            estado:    $request->integer('estado', 1),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateNotaRequest $request, int $id): JsonResponse
    {
        $dto = $this->updateHandler->handle(new UpdateNotaCommand(
            id:                $id,
            nota:              $request->filled('nota') ? $request->integer('nota') : null,
            nota_seg:          $request->filled('nota_seg') ? $request->integer('nota_seg') : null,
            paralelo:          $request->paralelo,
            mostrarcert_notas: $request->filled('mostrarcert_notas') ? $request->integer('mostrarcert_notas') : null,
            estado:            $request->filled('estado') ? $request->integer('estado') : null,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteNotaCommand($id));

        return response()->json(null, 204);
    }
}
