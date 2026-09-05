<?php

namespace App\Http\Controllers\Api;

use App\Application\RevistasCientificas\Commands\CreateRevistaCientificaCommand;
use App\Application\RevistasCientificas\Commands\DeleteRevistaCientificaCommand;
use App\Application\RevistasCientificas\Commands\UpdateRevistaCientificaCommand;
use App\Application\RevistasCientificas\Handlers\CreateRevistaCientificaHandler;
use App\Application\RevistasCientificas\Handlers\DeleteRevistaCientificaHandler;
use App\Application\RevistasCientificas\Handlers\UpdateRevistaCientificaHandler;
use App\Application\RevistasCientificas\Queries\GetRevistaCientificaByIdQuery;
use App\Application\RevistasCientificas\Queries\GetRevistasCientificasQuery;
use App\Application\RevistasCientificas\QueryHandlers\GetRevistaCientificaByIdQueryHandler;
use App\Application\RevistasCientificas\QueryHandlers\GetRevistasCientificasQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\RevistasCientificas\StoreRevistaCientificaRequest;
use App\Http\Requests\RevistasCientificas\UpdateRevistaCientificaRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RevistaCientificaController extends Controller
{
    public function __construct(
        private readonly GetRevistasCientificasQueryHandler   $listHandler,
        private readonly GetRevistaCientificaByIdQueryHandler $showHandler,
        private readonly CreateRevistaCientificaHandler       $createHandler,
        private readonly UpdateRevistaCientificaHandler       $updateHandler,
        private readonly DeleteRevistaCientificaHandler       $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 30),
        ]);

        return response()->json(
            $this->listHandler->handle(new GetRevistasCientificasQuery(
                pagination:   $pagination,
                query:        $request->get('query') ?: null,
                conInactivos: $request->boolean('conInactivos', false),
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->showHandler->handle(new GetRevistaCientificaByIdQuery($id))
        );
    }

    public function store(StoreRevistaCientificaRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateRevistaCientificaCommand(
            id_revistacientifica:          $request->integer('id_revistacientifica'),
            id_us_reg:                     $request->integer('id_us_reg', 0),
            num_revistacientifica:         $request->integer('num_revistacientifica', 0),
            titulo_revistacientifica:      $request->input('titulo_revistacientifica'),
            descripcion_revistacientifica: $request->input('descripcion_revistacientifica'),
            fecha_publicacion:             $request->input('fecha_publicacion'),
            archivo:                       $request->input('archivo'),
            estado:                        $request->integer('estado', 1),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateRevistaCientificaRequest $request, int $id): JsonResponse
    {
        return response()->json(
            $this->updateHandler->handle(new UpdateRevistaCientificaCommand(
                id:                            $id,
                titulo_revistacientifica:      $request->input('titulo_revistacientifica'),
                descripcion_revistacientifica: $request->input('descripcion_revistacientifica'),
                fecha_publicacion:             $request->input('fecha_publicacion'),
                archivo:                       $request->input('archivo'),
                estado:                        $request->has('estado') ? $request->integer('estado') : null,
            ))
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteRevistaCientificaCommand($id));

        return response()->json(null, 204);
    }
}
