<?php

namespace App\Http\Controllers\Api;

use App\Application\Boletines\Commands\CreateBoletinCommand;
use App\Application\Boletines\Commands\DeleteBoletinCommand;
use App\Application\Boletines\Commands\UpdateBoletinCommand;
use App\Application\Boletines\Handlers\CreateBoletinHandler;
use App\Application\Boletines\Handlers\DeleteBoletinHandler;
use App\Application\Boletines\Handlers\UpdateBoletinHandler;
use App\Application\Boletines\Queries\GetBoletinByIdQuery;
use App\Application\Boletines\Queries\GetBoletinBySlugQuery;
use App\Application\Boletines\Queries\GetBoletinesQuery;
use App\Application\Boletines\QueryHandlers\GetBoletinByIdQueryHandler;
use App\Application\Boletines\QueryHandlers\GetBoletinBySlugQueryHandler;
use App\Application\Boletines\QueryHandlers\GetBoletinesQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Boletines\StoreBoletinRequest;
use App\Http\Requests\Boletines\UpdateBoletinRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BoletinController extends Controller
{
    public function __construct(
        private readonly GetBoletinesQueryHandler     $listHandler,
        private readonly GetBoletinByIdQueryHandler   $showHandler,
        private readonly GetBoletinBySlugQueryHandler $showBySlugHandler,
        private readonly CreateBoletinHandler         $createHandler,
        private readonly UpdateBoletinHandler       $updateHandler,
        private readonly DeleteBoletinHandler       $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 30),
        ]);

        return response()->json(
            $this->listHandler->handle(new GetBoletinesQuery(
                pagination:   $pagination,
                query:        $request->get('query') ?: null,
                conInactivos: $request->boolean('conInactivos', false),
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->showHandler->handle(new GetBoletinByIdQuery($id))
        );
    }

    public function showBySlug(string $slug): JsonResponse
    {
        return response()->json(
            $this->showBySlugHandler->handle(new GetBoletinBySlugQuery($slug))
        );
    }

    public function store(StoreBoletinRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateBoletinCommand(
            id_boletin:          $request->integer('id_boletin'),
            id_us_reg:           $request->integer('id_us_reg', 0),
            num_boletin:         $request->integer('num_boletin', 0),
            titulo_pagina:       $request->input('titulo_pagina'),
            titulo_boletin:      $request->input('titulo_boletin'),
            descripcion_boletin: $request->input('descripcion_boletin'),
            estado:              $request->integer('estado', 1),
            imagen_url:          $request->input('imagen_url'),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateBoletinRequest $request, int $id): JsonResponse
    {
        return response()->json(
            $this->updateHandler->handle(new UpdateBoletinCommand(
                id:                  $id,
                titulo_pagina:       $request->input('titulo_pagina'),
                titulo_boletin:      $request->input('titulo_boletin'),
                descripcion_boletin: $request->input('descripcion_boletin'),
                estado:              $request->has('estado') ? $request->integer('estado') : null,
                imagen_url:          $request->input('imagen_url'),
            ))
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteBoletinCommand($id));

        return response()->json(null, 204);
    }
}
