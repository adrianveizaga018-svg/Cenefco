<?php

namespace App\Http\Controllers\Api;

use App\Application\Fotos\Commands\CreateFotoCommand;
use App\Application\Fotos\Commands\DeleteFotoCommand;
use App\Application\Fotos\Commands\UpdateFotoCommand;
use App\Application\Fotos\Handlers\CreateFotoHandler;
use App\Application\Fotos\Handlers\DeleteFotoHandler;
use App\Application\Fotos\Handlers\UpdateFotoHandler;
use App\Application\Fotos\Queries\GetFotoByIdQuery;
use App\Application\Fotos\Queries\GetFotosQuery;
use App\Application\Fotos\QueryHandlers\GetFotoByIdQueryHandler;
use App\Application\Fotos\QueryHandlers\GetFotosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Fotos\StoreFotoRequest;
use App\Http\Requests\Fotos\UpdateFotoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FotoController extends Controller
{
    public function __construct(
        private readonly GetFotosQueryHandler   $listHandler,
        private readonly GetFotoByIdQueryHandler $showHandler,
        private readonly CreateFotoHandler       $createHandler,
        private readonly UpdateFotoHandler       $updateHandler,
        private readonly DeleteFotoHandler       $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 50),
        ]);

        return response()->json(
            $this->listHandler->handle(new GetFotosQuery(
                pagination:   $pagination,
                query:        $request->get('query') ?: null,
                conInactivos: $request->boolean('conInactivos', false),
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->showHandler->handle(new GetFotoByIdQuery($id))
        );
    }

    public function store(StoreFotoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateFotoCommand(
            id_foto:          $request->integer('id_foto'),
            id_us_reg:        $request->integer('id_us_reg', 0),
            num_foto:         $request->integer('num_foto', 0),
            titulo_foto:      $request->input('titulo_foto'),
            descripcion_foto: $request->input('descripcion_foto'),
            foto:             $request->input('foto'),
            fecha_foto:       $request->input('fecha_foto'),
            estado:           $request->integer('estado', 1),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateFotoRequest $request, int $id): JsonResponse
    {
        return response()->json(
            $this->updateHandler->handle(new UpdateFotoCommand(
                id:               $id,
                titulo_foto:      $request->input('titulo_foto'),
                descripcion_foto: $request->input('descripcion_foto'),
                foto:             $request->input('foto'),
                fecha_foto:       $request->input('fecha_foto'),
                estado:           $request->has('estado') ? $request->integer('estado') : null,
            ))
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteFotoCommand($id));

        return response()->json(null, 204);
    }
}
