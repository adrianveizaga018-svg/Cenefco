<?php

namespace App\Http\Controllers\Api;

use App\Application\Trivia\Commands\IniciarTriviaPartidaCommand;
use App\Application\Trivia\Commands\ResponderTriviaPreguntaCommand;
use App\Application\Trivia\Handlers\IniciarTriviaPartidaHandler;
use App\Application\Trivia\Handlers\ResponderTriviaPreguntaHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Trivia\ResponderTriviaPreguntaRequest;
use App\Http\Requests\Trivia\StoreTriviaPartidaRequest;
use Illuminate\Http\JsonResponse;

class TriviaJuegoController extends Controller
{
    public function __construct(
        private readonly IniciarTriviaPartidaHandler $iniciarHandler,
        private readonly ResponderTriviaPreguntaHandler $responderHandler,
    ) {}

    public function iniciar(StoreTriviaPartidaRequest $request): JsonResponse
    {
        try {
            $resultado = $this->iniciarHandler->handle(new IniciarTriviaPartidaCommand(
                categoriaId: $request->categoria_id,
                usuarioId: $request->user()->id,
            ));

            return response()->json($resultado, 201);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 422);
        }
    }

    public function responder(ResponderTriviaPreguntaRequest $request, int $partidaId): JsonResponse
    {
        try {
            $resultado = $this->responderHandler->handle(new ResponderTriviaPreguntaCommand(
                partidaId: $partidaId,
                usuarioId: $request->user()->id,
                preguntaId: $request->pregunta_id,
                opcionId: $request->opcion_id,
                tiempoRespuestaMs: $request->tiempo_respuesta_ms,
            ));

            return response()->json($resultado);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 422);
        }
    }
}
