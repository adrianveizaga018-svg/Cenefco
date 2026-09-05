<?php

namespace App\Http\Controllers\Api;

use App\Application\HistoriaMunicipio\Commands\CreateHistoriaMunicipioCommand;
use App\Application\HistoriaMunicipio\Commands\DeleteHistoriaMunicipioCommand;
use App\Application\HistoriaMunicipio\Commands\UpdateHistoriaMunicipioCommand;
use App\Application\HistoriaMunicipio\Handlers\CreateHistoriaMunicipioHandler;
use App\Application\HistoriaMunicipio\Handlers\DeleteHistoriaMunicipioHandler;
use App\Application\HistoriaMunicipio\Handlers\UpdateHistoriaMunicipioHandler;
use App\Application\HistoriaMunicipio\Queries\GetHistoriaMunicipioByIdQuery;
use App\Application\HistoriaMunicipio\Queries\GetHistoriaMunicipioQuery;
use App\Application\HistoriaMunicipio\QueryHandlers\GetHistoriaMunicipioByIdQueryHandler;
use App\Application\HistoriaMunicipio\QueryHandlers\GetHistoriaMunicipioQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\HistoriaMunicipio\StoreHistoriaMunicipioRequest;
use App\Http\Requests\HistoriaMunicipio\UpdateHistoriaMunicipioRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HistoriaMunicipioController extends Controller
{
    public function __construct(
        private readonly GetHistoriaMunicipioQueryHandler $getListHandler,
        private readonly GetHistoriaMunicipioByIdQueryHandler $getByIdHandler,
        private readonly CreateHistoriaMunicipioHandler $createHandler,
        private readonly UpdateHistoriaMunicipioHandler $updateHandler,
        private readonly DeleteHistoriaMunicipioHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize' => $request->get('pageSize', 15),
            'query' => $request->get('query', ''),
        ]);

        return response()->json(
            $this->getListHandler->handle(new GetHistoriaMunicipioQuery($pagination))
        );
    }

    public function store(StoreHistoriaMunicipioRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateHistoriaMunicipioCommand(
            titulo: $request->titulo,
            contenido: $request->contenido,
            fecha_inicio: $request->fecha_inicio,
            fecha_fin: $request->fecha_fin,
            imagen_url: $request->imagen_url,
            orden: (int) $request->input('orden', 0),
            activo: $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetHistoriaMunicipioByIdQuery($id)));
    }

    public function update(UpdateHistoriaMunicipioRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateHistoriaMunicipioCommand($id, $request->validated())));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteHistoriaMunicipioCommand($id));

        return response()->json(null, 204);
    }
}
