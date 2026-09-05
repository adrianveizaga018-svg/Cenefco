<?php

namespace App\Http\Controllers\Api;

use App\Application\Intents\Commands\CreateIntentCommand;
use App\Application\Intents\Commands\DeleteIntentCommand;
use App\Application\Intents\Commands\UpdateIntentCommand;
use App\Application\Intents\Handlers\CreateIntentHandler;
use App\Application\Intents\Handlers\DeleteIntentHandler;
use App\Application\Intents\Handlers\UpdateIntentHandler;
use App\Application\Intents\Queries\GetIntentByIdQuery;
use App\Application\Intents\Queries\GetIntentsQuery;
use App\Application\Intents\QueryHandlers\GetIntentByIdQueryHandler;
use App\Application\Intents\QueryHandlers\GetIntentsQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Intents\StoreIntentRequest;
use App\Http\Requests\Intents\UpdateIntentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IntentController extends Controller
{
    public function __construct(
        private readonly GetIntentsQueryHandler $listHandler,
        private readonly GetIntentByIdQueryHandler $showHandler,
        private readonly CreateIntentHandler $createHandler,
        private readonly UpdateIntentHandler $updateHandler,
        private readonly DeleteIntentHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->listHandler->handle(new GetIntentsQuery(
            query:   $request->input('query'),
            dominio: $request->input('dominio'),
        )));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->showHandler->handle(new GetIntentByIdQuery($id)));
    }

    public function store(StoreIntentRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateIntentCommand(
            nombre:              $request->string('nombre')->toString(),
            slug:                $request->string('slug')->toString(),
            dominio:             $request->string('dominio')->toString(),
            prioridad:           $request->integer('prioridad'),
            accion:              $request->string('accion')->toString(),
            activo:              $request->boolean('activo', true),
            orden:               $request->integer('orden', 0),
            eventos:             $request->input('eventos', []),
            inputContexts:       $request->input('input_contexts', []),
            outputContexts:      $request->input('output_contexts', []),
            frasesEntrenamiento: $request->input('frases_entrenamiento', []),
            respuestas:          $request->input('respuestas', []),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateIntentRequest $request, int $id): JsonResponse
    {
        \Log::info('IntentController@update', [
            'id'         => $id,
            'respuestas' => $request->get('respuestas'),
            'nombre'     => $request->get('nombre'),
            'all_keys'   => array_keys($request->all()),
        ]);

        $dto = $this->updateHandler->handle(new UpdateIntentCommand(
            id:                  $id,
            nombre:              $request->string('nombre')->toString(),
            slug:                $request->string('slug')->toString(),
            dominio:             $request->string('dominio')->toString(),
            prioridad:           $request->integer('prioridad'),
            accion:              $request->string('accion')->toString(),
            activo:              $request->boolean('activo', true),
            orden:               $request->integer('orden', 0),
            eventos:             $request->input('eventos', []),
            inputContexts:       $request->input('input_contexts', []),
            outputContexts:      $request->input('output_contexts', []),
            frasesEntrenamiento: $request->input('frases_entrenamiento', []),
            respuestas:          $request->input('respuestas', []),
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteIntentCommand($id));

        return response()->json(['message' => 'Intent eliminado.']);
    }

    public function toggleActivo(int $id): JsonResponse
    {
        $dto = $this->showHandler->handle(new GetIntentByIdQuery($id));
        $current = $dto;
        $newActivo = ! $current->activo;

        $updated = $this->updateHandler->handle(new UpdateIntentCommand(
            id:                  $id,
            nombre:              $current->nombre,
            slug:                $current->slug,
            dominio:             $current->dominio,
            prioridad:           $current->prioridad,
            accion:              $current->accion,
            activo:              $newActivo,
            orden:               $current->orden,
            eventos:             $current->eventos,
            inputContexts:       $current->inputContexts,
            outputContexts:      $current->outputContexts,
            frasesEntrenamiento: $current->frasesEntrenamiento,
            respuestas:          $current->respuestas,
        ));

        return response()->json(['activo' => $updated->activo]);
    }
}
