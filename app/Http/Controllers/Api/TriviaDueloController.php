<?php

namespace App\Http\Controllers\Api;

use App\Application\Trivia\Commands\CrearDueloTriviaCommand;
use App\Application\Trivia\Commands\ResponderDueloTriviaCommand;
use App\Application\Trivia\Commands\UnirseDueloTriviaCommand;
use App\Application\Trivia\Handlers\CrearDueloTriviaHandler;
use App\Application\Trivia\Handlers\ResponderDueloTriviaHandler;
use App\Application\Trivia\Handlers\UnirseDueloTriviaHandler;
use App\Application\Trivia\Queries\GetTriviaDueloEstadoQuery;
use App\Application\Trivia\QueryHandlers\GetTriviaDueloEstadoQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Trivia\ResponderTriviaPreguntaRequest;
use App\Http\Requests\Trivia\StoreTriviaDueloRequest;
use App\Http\Requests\Trivia\UnirseTriviaDueloRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TriviaDueloController extends Controller
{
    public function __construct(
        private readonly CrearDueloTriviaHandler $crearHandler,
        private readonly UnirseDueloTriviaHandler $unirseHandler,
        private readonly ResponderDueloTriviaHandler $responderHandler,
        private readonly GetTriviaDueloEstadoQueryHandler $getEstadoHandler,
    ) {}

    public function crear(StoreTriviaDueloRequest $request): JsonResponse
    {
        try {
            $dto = $this->crearHandler->handle(new CrearDueloTriviaCommand(
                categoriaId: (int) $request->categoria_id,
                usuarioId: $request->user()->id,
            ));

            return response()->json($dto, 201);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 422);
        }
    }

    public function unirse(UnirseTriviaDueloRequest $request): JsonResponse
    {
        try {
            $dto = $this->unirseHandler->handle(new UnirseDueloTriviaCommand(
                codigoSala: strtoupper($request->string('codigo_sala')->toString()),
                usuarioId: $request->user()->id,
            ));

            return response()->json($dto);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 422);
        }
    }

    public function responder(ResponderTriviaPreguntaRequest $request, int $partidaId): JsonResponse
    {
        try {
            $dto = $this->responderHandler->handle(new ResponderDueloTriviaCommand(
                partidaId: $partidaId,
                usuarioId: $request->user()->id,
                preguntaId: (int) $request->pregunta_id,
                opcionId: $request->filled('opcion_id') ? (int) $request->opcion_id : null,
                tiempoRespuestaMs: (int) $request->get('tiempo_respuesta_ms', 0),
            ));

            return response()->json($dto);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 422);
        }
    }

    public function estado(Request $request, int $partidaId): JsonResponse
    {
        $dto = $this->getEstadoHandler->handle(new GetTriviaDueloEstadoQuery(
            partidaId: $partidaId,
            usuarioId: $request->user()->id,
        ));

        return response()->json($dto);
    }
}
