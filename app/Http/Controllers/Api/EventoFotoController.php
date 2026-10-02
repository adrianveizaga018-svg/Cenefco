<?php

namespace App\Http\Controllers\Api;

use App\Application\EventosFotos\Commands\CreateEventoFotoCommand;
use App\Application\EventosFotos\Commands\DeleteEventoFotoCommand;
use App\Application\EventosFotos\Commands\UpdateEventoFotoCommand;
use App\Application\EventosFotos\Handlers\CreateEventoFotoHandler;
use App\Application\EventosFotos\Handlers\DeleteEventoFotoHandler;
use App\Application\EventosFotos\Handlers\UpdateEventoFotoHandler;
use App\Application\EventosFotos\Queries\GetEventoFotoByIdQuery;
use App\Application\EventosFotos\Queries\GetFotosByEventoQuery;
use App\Application\EventosFotos\QueryHandlers\GetEventoFotoByIdQueryHandler;
use App\Application\EventosFotos\QueryHandlers\GetFotosByEventoQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventosFotos\StoreEventoFotoRequest;
use App\Http\Requests\EventosFotos\UpdateEventoFotoRequest;
use Illuminate\Http\JsonResponse;

class EventoFotoController extends Controller
{
    public function __construct(
        private readonly GetFotosByEventoQueryHandler $getByEventoHandler,
        private readonly GetEventoFotoByIdQueryHandler $getByIdHandler,
        private readonly CreateEventoFotoHandler $createHandler,
        private readonly UpdateEventoFotoHandler $updateHandler,
        private readonly DeleteEventoFotoHandler $deleteHandler,
    ) {}

    public function index(int $eventoId): JsonResponse
    {
        return response()->json($this->getByEventoHandler->handle(new GetFotosByEventoQuery($eventoId)));
    }

    public function store(StoreEventoFotoRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateEventoFotoCommand(
            evento_id: $request->evento_id,
            archivo_url: $request->archivo_url,
            titulo: $request->titulo,
            orden: $request->integer('orden', 0),
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetEventoFotoByIdQuery($id)));
    }

    public function update(UpdateEventoFotoRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateEventoFotoCommand(
            id:          $id,
            archivo_url: $request->archivo_url,
            titulo:      $request->titulo,
            orden:       $request->has('orden') ? $request->integer('orden') : null,
        )));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteEventoFotoCommand($id));

        return response()->json(null, 204);
    }
}
