<?php

namespace App\Http\Controllers\Api;

use App\Application\Articulos\Commands\CreateArticuloCommand;
use App\Application\Articulos\Commands\DeleteArticuloCommand;
use App\Application\Articulos\Commands\UpdateArticuloCommand;
use App\Application\Articulos\Handlers\CreateArticuloHandler;
use App\Application\Articulos\Handlers\DeleteArticuloHandler;
use App\Application\Articulos\Handlers\UpdateArticuloHandler;
use App\Application\Articulos\Queries\GetArticuloByIdQuery;
use App\Application\Articulos\Queries\GetArticuloBySlugQuery;
use App\Application\Articulos\Queries\GetArticulosQuery;
use App\Application\Articulos\QueryHandlers\GetArticuloByIdQueryHandler;
use App\Application\Articulos\QueryHandlers\GetArticuloBySlugQueryHandler;
use App\Application\Articulos\QueryHandlers\GetArticulosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Articulos\StoreArticuloRequest;
use App\Http\Requests\Articulos\UpdateArticuloRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticuloController extends Controller
{
    public function __construct(
        private readonly GetArticulosQueryHandler      $listHandler,
        private readonly GetArticuloByIdQueryHandler   $showHandler,
        private readonly GetArticuloBySlugQueryHandler $showBySlugHandler,
        private readonly CreateArticuloHandler         $createHandler,
        private readonly UpdateArticuloHandler         $updateHandler,
        private readonly DeleteArticuloHandler         $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 15),
        ]);

        return response()->json(
            $this->listHandler->handle(new GetArticulosQuery(
                pagination:   $pagination,
                query:        $request->get('query') ?: null,
                estadoWeb:    $request->has('estado_web') ? $request->get('estado_web') : null,
                conInactivos: $request->boolean('conInactivos', false),
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->showHandler->handle(new GetArticuloByIdQuery($id))
        );
    }

    public function showBySlug(string $slug): JsonResponse
    {
        return response()->json(
            $this->showBySlugHandler->handle(new GetArticuloBySlugQuery($slug))
        );
    }

    public function store(StoreArticuloRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateArticuloCommand(
            titulo:               $request->input('titulo'),
            slug:                 $request->input('slug'),
            entradilla:           $request->input('entradilla'),
            contenido:            $request->input('contenido'),
            imagen_principal_url: $request->input('imagen_principal_url'),
            imagen_alt:           $request->input('imagen_alt'),
            destacada:            $request->boolean('destacada', false),
            fecha_publicacion:    $request->input('fecha_publicacion'),
            estado_web:           $request->input('estado_web', 'borrador'),
            meta_titulo:          $request->input('meta_titulo'),
            meta_descripcion:     $request->input('meta_descripcion'),
            estado:               $request->integer('estado', 1),
            etiquetas:            $request->input('etiquetas', []),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateArticuloRequest $request, int $id): JsonResponse
    {
        return response()->json(
            $this->updateHandler->handle(new UpdateArticuloCommand(
                id:                   $id,
                titulo:               $request->input('titulo'),
                slug:                 $request->input('slug'),
                entradilla:           $request->input('entradilla'),
                contenido:            $request->input('contenido'),
                imagen_principal_url: $request->input('imagen_principal_url'),
                imagen_alt:           $request->input('imagen_alt'),
                destacada:            $request->has('destacada') ? $request->boolean('destacada') : null,
                fecha_publicacion:    $request->input('fecha_publicacion'),
                estado_web:           $request->input('estado_web'),
                meta_titulo:          $request->input('meta_titulo'),
                meta_descripcion:     $request->input('meta_descripcion'),
                estado:               $request->has('estado') ? $request->integer('estado') : null,
                etiquetas:            $request->has('etiquetas') ? $request->input('etiquetas') : null,
            ))
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteArticuloCommand($id));

        return response()->json(null, 204);
    }
}
