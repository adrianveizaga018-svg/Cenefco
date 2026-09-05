<?php

namespace App\Http\Controllers\Api;

use App\Application\NotasPrensa\Commands\CreateNotaPrensaCommand;
use App\Application\NotasPrensa\Commands\DeleteNotaPrensaCommand;
use App\Application\NotasPrensa\Commands\UpdateNotaPrensaCommand;
use App\Application\NotasPrensa\Handlers\CreateNotaPrensaHandler;
use App\Application\NotasPrensa\Handlers\DeleteNotaPrensaHandler;
use App\Application\NotasPrensa\Handlers\UpdateNotaPrensaHandler;
use App\Application\NotasPrensa\Queries\GetNotaPrensaByIdQuery;
use App\Application\NotasPrensa\Queries\GetNotasPrensaQuery;
use App\Application\NotasPrensa\QueryHandlers\GetNotaPrensaByIdQueryHandler;
use App\Application\NotasPrensa\QueryHandlers\GetNotasPrensaQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\NotasPrensa\StoreNotaPrensaRequest;
use App\Http\Requests\NotasPrensa\UpdateNotaPrensaRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotaPrensaController extends Controller
{
    public function __construct(
        private readonly GetNotasPrensaQueryHandler $getNotasHandler,
        private readonly GetNotaPrensaByIdQueryHandler $getByIdHandler,
        private readonly CreateNotaPrensaHandler $createHandler,
        private readonly UpdateNotaPrensaHandler $updateHandler,
        private readonly DeleteNotaPrensaHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 20),
            'query'     => $request->get('query', ''),
            'sortKey'   => $request->input('sort.key', 'fecha_publicacion'),
            'sortOrder' => $request->input('sort.order', 'desc'),
        ]);

        return response()->json(
            $this->getNotasHandler->handle(new GetNotasPrensaQuery(
                $pagination,
                $request->boolean('soloDestacadas', false),
                $request->boolean('soloActivos', false),
            ))
        );
    }

    public function store(StoreNotaPrensaRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateNotaPrensaCommand(
            titulo:            $request->titulo,
            medio:             $request->medio,
            fecha_publicacion: $request->fecha_publicacion,
            logo_medio_url:    $request->logo_medio_url,
            logo_medio_alt:    $request->logo_medio_alt,
            resumen:           $request->resumen,
            url_noticia:       $request->url_noticia,
            destacada:         $request->boolean('destacada', false),
            orden:             $request->integer('orden', 0),
            activo:            $request->boolean('activo', true),
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetNotaPrensaByIdQuery($id)));
    }

    public function update(UpdateNotaPrensaRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateNotaPrensaCommand(
            id:                $id,
            titulo:            $request->titulo,
            medio:             $request->medio,
            fecha_publicacion: $request->fecha_publicacion,
            logo_medio_url:    $request->logo_medio_url,
            logo_medio_alt:    $request->logo_medio_alt,
            resumen:           $request->resumen,
            url_noticia:       $request->url_noticia,
            destacada:         $request->has('destacada') ? $request->boolean('destacada') : null,
            orden:             $request->has('orden') ? $request->integer('orden') : null,
            activo:            $request->has('activo') ? $request->boolean('activo') : null,
        )));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteNotaPrensaCommand($id));

        return response()->json(null, 204);
    }
}
