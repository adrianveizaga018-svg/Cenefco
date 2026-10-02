<?php

namespace App\Http\Controllers\Api;

use App\Application\Noticias\Commands\CreateNoticiaCommand;
use App\Application\Noticias\Commands\DeleteNoticiaCommand;
use App\Application\Noticias\Commands\UpdateNoticiaCommand;
use App\Application\Noticias\Handlers\CreateNoticiaHandler;
use App\Application\Noticias\Handlers\DeleteNoticiaHandler;
use App\Application\Noticias\Handlers\UpdateNoticiaHandler;
use App\Application\Noticias\Queries\GetNoticiaByIdQuery;
use App\Application\Noticias\Queries\GetNoticiaBySlugQuery;
use App\Application\Noticias\Queries\GetNoticiasQuery;
use App\Application\Noticias\QueryHandlers\GetNoticiaByIdQueryHandler;
use App\Application\Noticias\QueryHandlers\GetNoticiaBySlugQueryHandler;
use App\Application\Noticias\QueryHandlers\GetNoticiasQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Noticias\StoreNoticiaRequest;
use App\Http\Requests\Noticias\UpdateNoticiaRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoticiaController extends Controller
{
    public function __construct(
        private readonly GetNoticiasQueryHandler $getNoticiasHandler,
        private readonly GetNoticiaByIdQueryHandler $getNoticiaByIdHandler,
        private readonly GetNoticiaBySlugQueryHandler $getNoticiaBySlugHandler,
        private readonly CreateNoticiaHandler $createHandler,
        private readonly UpdateNoticiaHandler $updateHandler,
        private readonly DeleteNoticiaHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize' => $request->get('pageSize', 15),
            'query' => $request->get('query', ''),
            'sortKey' => $request->input('sort.key', 'fecha_publicacion'),
            'sortOrder' => $request->input('sort.order', 'desc'),
        ]);

        return response()->json(
            $this->getNoticiasHandler->handle(
                new GetNoticiasQuery($pagination, $request->boolean('soloActivos', false))
            )
        );
    }

    public function store(StoreNoticiaRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateNoticiaCommand(
            categoria_id: $request->categoria_id,
            autor_id: auth()->id() ?? 1,
            titulo: $request->titulo,
            entradilla: $request->entradilla,
            cuerpo: $request->cuerpo,
            imagen_principal_url: $request->imagen_principal_url,
            imagen_alt: $request->imagen_alt,
            estado: $request->estado ?? 'borrador',
            destacada: $request->boolean('destacada', false),
            fecha_publicacion: $request->fecha_publicacion,
            meta_titulo: $request->meta_titulo,
            meta_descripcion: $request->meta_descripcion,
            etiquetas: $request->get('etiquetas', []),
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getNoticiaByIdHandler->handle(new GetNoticiaByIdQuery($id)));
    }

    public function showBySlug(string $slug): JsonResponse
    {
        return response()->json($this->getNoticiaBySlugHandler->handle(new GetNoticiaBySlugQuery($slug)));
    }

    public function update(UpdateNoticiaRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateNoticiaCommand(
            id: $id,
            data: $request->safe()->except(['etiquetas']),
            etiquetas: $request->get('etiquetas', [])
        )));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteNoticiaCommand($id));

        return response()->json(null, 204);
    }
}
