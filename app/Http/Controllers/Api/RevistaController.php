<?php

namespace App\Http\Controllers\Api;

use App\Application\Revistas\Commands\CreateRevistaCommand;
use App\Application\Revistas\Commands\DeleteRevistaCommand;
use App\Application\Revistas\Commands\UpdateRevistaCommand;
use App\Application\Revistas\Handlers\CreateRevistaHandler;
use App\Application\Revistas\Handlers\DeleteRevistaHandler;
use App\Application\Revistas\Handlers\UpdateRevistaHandler;
use App\Application\Revistas\Queries\GetRevistaByIdQuery;
use App\Application\Revistas\Queries\GetRevistasQuery;
use App\Application\Revistas\QueryHandlers\GetRevistaByIdQueryHandler;
use App\Application\Revistas\QueryHandlers\GetRevistasQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Revistas\StoreRevistaRequest;
use App\Http\Requests\Revistas\UpdateRevistaRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RevistaController extends Controller
{
    public function __construct(
        private readonly GetRevistasQueryHandler   $listHandler,
        private readonly GetRevistaByIdQueryHandler $showHandler,
        private readonly CreateRevistaHandler       $createHandler,
        private readonly UpdateRevistaHandler       $updateHandler,
        private readonly DeleteRevistaHandler       $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 30),
        ]);

        return response()->json(
            $this->listHandler->handle(new GetRevistasQuery(
                pagination:   $pagination,
                query:        $request->get('query') ?: null,
                conInactivos: $request->boolean('conInactivos', false),
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->showHandler->handle(new GetRevistaByIdQuery($id))
        );
    }

    public function store(StoreRevistaRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateRevistaCommand(
            id_revista:          $request->integer('id_revista'),
            id_us_reg:           $request->integer('id_us_reg', 0),
            num_revista:         $request->integer('num_revista', 0),
            titulo_revista:      $request->input('titulo_revista'),
            descripcion_revista: $request->input('descripcion_revista'),
            fecha_publicacion:   $request->input('fecha_publicacion'),
            archivo:             $request->input('archivo'),
            estado:              $request->integer('estado', 1),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateRevistaRequest $request, int $id): JsonResponse
    {
        return response()->json(
            $this->updateHandler->handle(new UpdateRevistaCommand(
                id:                  $id,
                titulo_revista:      $request->input('titulo_revista'),
                descripcion_revista: $request->input('descripcion_revista'),
                fecha_publicacion:   $request->input('fecha_publicacion'),
                archivo:             $request->input('archivo'),
                estado:              $request->has('estado') ? $request->integer('estado') : null,
            ))
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteRevistaCommand($id));

        return response()->json(null, 204);
    }
}
