<?php

namespace App\Http\Controllers\Api;

use App\Application\Eventos\Commands\CreateEventoCommand;
use App\Application\Eventos\Commands\DeleteEventoCommand;
use App\Application\Eventos\Commands\UpdateEventoCommand;
use App\Application\Eventos\Handlers\CreateEventoHandler;
use App\Application\Eventos\Handlers\DeleteEventoHandler;
use App\Application\Eventos\Handlers\UpdateEventoHandler;
use App\Application\Eventos\Queries\GetEventoByIdQuery;
use App\Application\Eventos\Queries\GetEventoBySlugQuery;
use App\Application\Eventos\Queries\GetEventosQuery;
use App\Application\Eventos\QueryHandlers\GetEventoByIdQueryHandler;
use App\Application\Eventos\QueryHandlers\GetEventoBySlugQueryHandler;
use App\Application\Eventos\QueryHandlers\GetEventosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Eventos\StoreEventoRequest;
use App\Http\Requests\Eventos\UpdateEventoRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    public function __construct(
        private readonly GetEventosQueryHandler      $getEventosHandler,
        private readonly GetEventoByIdQueryHandler   $getEventoByIdHandler,
        private readonly GetEventoBySlugQueryHandler $getEventoBySlugHandler,
        private readonly CreateEventoHandler         $createHandler,
        private readonly UpdateEventoHandler         $updateHandler,
        private readonly DeleteEventoHandler         $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize' => $request->get('pageSize', 15),
            'query' => $request->get('query', ''),
            'sortKey' => $request->input('sort.key', 'fecha_inicio'),
            'sortOrder' => $request->input('sort.order', 'desc'),
        ]);

        return response()->json(
            $this->getEventosHandler->handle(
                new GetEventosQuery($pagination, $request->boolean('soloActivos', false))
            )
        );
    }

    public function store(StoreEventoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateEventoCommand(
            titulo:           $request->titulo,
            entradilla:       $request->entradilla,
            descripcion:      $request->descripcion,
            imagen_url:       $request->imagen_url,
            imagen_alt:       $request->imagen_alt,
            tipo:             $request->tipo,
            modalidad:        $request->input('modalidad', 'presencial'),
            lugar:            $request->lugar,
            url_transmision:  $request->url_transmision,
            url_registro:     $request->url_registro,
            gratuito:         $request->boolean('gratuito', true),
            precio:           $request->precio,
            cupo_maximo:      $request->cupo_maximo ? (int) $request->cupo_maximo : null,
            programa_id:      $request->programa_id ? (int) $request->programa_id : null,
            fecha_inicio:     $request->fecha_inicio,
            fecha_fin:        $request->fecha_fin,
            todo_el_dia:      $request->boolean('todo_el_dia', false),
            destacado:        $request->boolean('destacado', false),
            meta_titulo:      $request->meta_titulo,
            meta_descripcion: $request->meta_descripcion,
            estado:           $request->input('estado', 'programado'),
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getEventoByIdHandler->handle(new GetEventoByIdQuery($id)));
    }

    public function showBySlug(string $slug): JsonResponse
    {
        return response()->json($this->getEventoBySlugHandler->handle(new GetEventoBySlugQuery($slug)));
    }

    public function update(UpdateEventoRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateEventoCommand(
            id:               $id,
            titulo:           $request->titulo,
            entradilla:       $request->entradilla,
            descripcion:      $request->descripcion,
            imagen_url:       $request->imagen_url,
            imagen_alt:       $request->imagen_alt,
            tipo:             $request->tipo,
            modalidad:        $request->modalidad,
            lugar:            $request->lugar,
            url_transmision:  $request->url_transmision,
            url_registro:     $request->url_registro,
            gratuito:         $request->has('gratuito') ? $request->boolean('gratuito') : null,
            precio:           $request->precio,
            cupo_maximo:      $request->has('cupo_maximo') ? (int) $request->cupo_maximo : null,
            programa_id:      $request->has('programa_id') ? (int) $request->programa_id : null,
            fecha_inicio:     $request->fecha_inicio,
            fecha_fin:        $request->fecha_fin,
            todo_el_dia:      $request->has('todo_el_dia') ? $request->boolean('todo_el_dia') : null,
            destacado:        $request->has('destacado') ? $request->boolean('destacado') : null,
            meta_titulo:      $request->meta_titulo,
            meta_descripcion: $request->meta_descripcion,
            estado:           $request->estado,
        )));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteEventoCommand($id));

        return response()->json(null, 204);
    }
}
