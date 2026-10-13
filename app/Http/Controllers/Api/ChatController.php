<?php

namespace App\Http\Controllers\Api;

use App\Application\Chat\Services\ChatService;
use App\Http\Controllers\Controller;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Chat interno del personal. Cada respuesta incluye solo lo que el usuario puede ver. */
class ChatController extends Controller
{
    public function __construct(private readonly ChatService $chat) {}

    public function conversaciones(Request $request): JsonResponse
    {
        return response()->json([
            'data'          => $this->chat->conversaciones($request->user()),
            'administra'    => $this->chat->administra($request->user()),
        ]);
    }

    public function noLeidos(Request $request): JsonResponse
    {
        return response()->json(['no_leidos' => $this->chat->totalNoLeidos($request->user())]);
    }

    public function directorio(Request $request): JsonResponse
    {
        return response()->json($this->chat->directorio($request->user(), $request->query('q')));
    }

    public function directo(Request $request): JsonResponse
    {
        $datos = $request->validate(['usuario_id' => 'required|integer']);

        return $this->responder(fn () => ['id' => $this->chat->directo($request->user(), (int) $datos['usuario_id'])]);
    }

    public function crearGrupo(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'nombre'      => 'required|string|max:120',
            'descripcion' => 'nullable|string|max:300',
            'miembros'    => 'array|max:300',
            'miembros.*'  => 'integer',
        ]);

        return $this->responder(fn () => ['id' => $this->chat->crearGrupo($request->user(), $datos['nombre'], $datos['descripcion'] ?? null, $datos['miembros'] ?? [])], 201);
    }

    public function actualizarGrupo(Request $request, int $id): JsonResponse
    {
        $datos = $request->validate([
            'nombre'      => 'nullable|string|max:120',
            'descripcion' => 'nullable|string|max:300',
            'archivada'   => 'nullable|boolean',
            'agregar'     => 'array', 'agregar.*' => 'integer',
            'quitar'      => 'array', 'quitar.*' => 'integer',
        ]);

        return $this->responder(function () use ($request, $id, $datos) {
            $this->chat->actualizarGrupo($request->user(), $id, $datos);

            return ['message' => 'Grupo actualizado.'];
        });
    }

    public function mensajes(Request $request, int $id): JsonResponse
    {
        return $this->responder(fn () => $this->chat->mensajes(
            $request->user(), $id,
            $request->integer('antes_de') ?: null,
            $request->integer('despues_de') ?: null,
        ), 200, 404);
    }

    public function enviar(Request $request, int $id): JsonResponse
    {
        $datos = $request->validate([
            'cuerpo'        => 'nullable|string|max:5000',
            'adjunto'       => 'nullable|file|max:15360|mimes:jpg,jpeg,png,webp,gif,pdf,doc,docx,xls,xlsx,csv,ppt,pptx,txt,zip,mp3,ogg,m4a,mp4',
            'responde_a_id' => 'nullable|integer',
            'menciones'     => 'array|max:50', 'menciones.*' => 'integer',
        ]);

        return $this->responder(fn () => $this->chat->enviar(
            $request->user(), $id, $datos['cuerpo'] ?? null, $request->file('adjunto'),
            isset($datos['responde_a_id']) ? (int) $datos['responde_a_id'] : null, $datos['menciones'] ?? [],
        ), 201);
    }

    public function leido(Request $request, int $id): JsonResponse
    {
        $datos = $request->validate(['ultimo_id' => 'required|integer|min:1']);

        return $this->responder(function () use ($request, $id, $datos) {
            $this->chat->marcarLeido($request->user(), $id, (int) $datos['ultimo_id']);

            return ['ok' => true];
        });
    }

    public function eliminar(Request $request, int $idMensaje): JsonResponse
    {
        return $this->responder(function () use ($request, $idMensaje) {
            $this->chat->eliminar($request->user(), $idMensaje);

            return ['message' => 'Mensaje eliminado.'];
        });
    }

    public function adjunto(Request $request, int $idMensaje): StreamedResponse|JsonResponse
    {
        try {
            $a = $this->chat->adjunto($request->user(), $idMensaje);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }

        $disposicion = $request->boolean('descargar') ? 'attachment' : 'inline';

        return Storage::disk(ChatService::DISCO)->response($a['ruta'], $a['nombre'], [
            'Content-Type' => $a['tipo'] ?: 'application/octet-stream',
            'X-Content-Type-Options' => 'nosniff',
        ], $disposicion);
    }

    public function buscar(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->chat->buscar($request->user(), (string) $request->query('q', ''))]);
    }

    private function responder(callable $accion, int $ok = 200, int $error = 422): JsonResponse
    {
        try {
            return response()->json($accion(), $ok);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getMessage() === 'Conversación no encontrada.' ? 404 : $error);
        }
    }
}
