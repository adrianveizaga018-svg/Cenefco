<?php

namespace App\Http\Controllers\Api;

use App\Application\Monografias\Commands\CreateMonografiaCommand;
use App\Application\Monografias\Commands\DeleteMonografiaCommand;
use App\Application\Monografias\Commands\UpdateMonografiaCommand;
use App\Application\Monografias\Handlers\CreateMonografiaHandler;
use App\Application\Monografias\Handlers\DeleteMonografiaHandler;
use App\Application\Monografias\Handlers\UpdateMonografiaHandler;
use App\Application\Monografias\Queries\GetMonografiaByIdQuery;
use App\Application\Monografias\Queries\GetMonografiasQuery;
use App\Application\Monografias\QueryHandlers\GetMonografiaByIdQueryHandler;
use App\Application\Monografias\QueryHandlers\GetMonografiasQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Monografias\StoreMonografiaRequest;
use App\Http\Requests\Monografias\UpdateMonografiaRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MonografiaController extends Controller
{
    public function __construct(
        private readonly GetMonografiasQueryHandler   $listHandler,
        private readonly GetMonografiaByIdQueryHandler $showHandler,
        private readonly CreateMonografiaHandler       $createHandler,
        private readonly UpdateMonografiaHandler       $updateHandler,
        private readonly DeleteMonografiaHandler       $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 30),
        ]);

        return response()->json(
            $this->listHandler->handle(new GetMonografiasQuery(
                pagination:   $pagination,
                query:        $request->get('query') ?: null,
                conInactivos: $request->boolean('conInactivos', false),
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->showHandler->handle(new GetMonografiaByIdQuery($id))
        );
    }

    public function store(StoreMonografiaRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateMonografiaCommand(
            id_monografia:          $request->integer('id_monografia'),
            id_us_reg:              $request->integer('id_us_reg', 0),
            num_monografia:         $request->integer('num_monografia', 0),
            titulo_monografia:      $request->input('titulo_monografia'),
            descripcion_monografia: $request->input('descripcion_monografia'),
            fecha_publicacion:      $request->input('fecha_publicacion'),
            autor:                  $request->input('autor'),
            archivo:                $request->input('archivo'),
            estado:                 $request->integer('estado', 1),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateMonografiaRequest $request, int $id): JsonResponse
    {
        return response()->json(
            $this->updateHandler->handle(new UpdateMonografiaCommand(
                id:                     $id,
                titulo_monografia:      $request->input('titulo_monografia'),
                descripcion_monografia: $request->input('descripcion_monografia'),
                fecha_publicacion:      $request->input('fecha_publicacion'),
                autor:                  $request->input('autor'),
                archivo:                $request->input('archivo'),
                estado:                 $request->has('estado') ? $request->integer('estado') : null,
            ))
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteMonografiaCommand($id));

        return response()->json(null, 204);
    }
}
