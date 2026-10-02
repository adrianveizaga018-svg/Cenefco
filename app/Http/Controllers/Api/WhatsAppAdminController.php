<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WhatsApp\AsignarEtiquetasRequest;
use App\Http\Requests\WhatsApp\EnviarMasivoRequest;
use App\Http\Requests\WhatsApp\EnviarMediaRequest;
use App\Http\Requests\WhatsApp\EnviarMensajeRequest;
use App\Http\Requests\WhatsApp\StoreEtiquetaRequest;
use App\Http\Requests\WhatsApp\UpdateEtiquetaRequest;
use App\Infrastructure\Shared\Services\WhatsAppService;
use App\Infrastructure\WhatsApp\Models\WhatsappConversacion;
use App\Infrastructure\WhatsApp\Models\WhatsappEtiqueta;
use App\Infrastructure\WhatsApp\Models\WhatsappMensaje;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WhatsAppAdminController extends Controller
{
    public function conversaciones(Request $request): JsonResponse
    {
        $q = WhatsappConversacion::query()
            ->with('etiquetas')
            ->orderByRaw("CASE WHEN estado = 'soporte' THEN 0 ELSE 1 END")
            ->orderByDesc('updated_at');

        if ($search = $request->get('query')) {
            $q->where(function ($sub) use ($search) {
                $sub->where('phone', 'like', "%{$search}%")
                    ->orWhereHas('cliente', fn ($c) => $c->where('nombre', 'like', "%{$search}%")
                        ->orWhere('razon_social', 'like', "%{$search}%")
                    );
            });
        }

        if ($estado = $request->get('estado')) {
            $q->where('estado', $estado);
        }

        if ($etiquetaId = $request->get('etiqueta_id')) {
            $q->whereHas('etiquetas', fn ($sub) => $sub->where('whatsapp_etiquetas.id', $etiquetaId));
        }

        $pageSize  = (int) $request->get('pageSize', 15);
        $pageIndex = (int) $request->get('pageIndex', 1);

        $paginated  = $q->paginate($pageSize, ['*'], 'page', $pageIndex);
        $asesorIds  = collect($paginated->items())->pluck('asesor_id')->filter()->unique()->values();
        $asesores   = $asesorIds->isNotEmpty()
            ? DB::table('web_asesor')->whereIn('id', $asesorIds)->get()->keyBy('id')
            : collect();

        $data = collect($paginated->items())->map(fn ($c) => [
            'id'           => $c->id,
            'phone'        => $c->phone,
            'phone_display'=> $c->phone_display,
            'nombre'       => $c->nombre,
            'estado'     => $c->estado,
            'contexto'   => $c->contexto,
            'cliente_id' => $c->cliente_id,
            'asesor_id'  => $c->asesor_id,
            'asesor'     => $c->asesor_id ? $asesores->get($c->asesor_id) : null,
            'etiquetas'  => $c->etiquetas->map(fn ($e) => [
                'id'     => $e->id,
                'nombre' => $e->nombre,
                'color'  => $e->color,
            ])->all(),
            'updated_at' => $c->updated_at?->toIso8601String(),
            'created_at' => $c->created_at?->toIso8601String(),
        ])->all();

        return response()->json([
            'data'  => $data,
            'total' => $paginated->total(),
        ]);
    }

    public function mensajes(int $id): JsonResponse
    {
        $conv = WhatsappConversacion::findOrFail($id);

        $mensajes = WhatsappMensaje::where('conversacion_id', $conv->id)
            ->orderBy('created_at')
            ->get(['id', 'direccion', 'tipo', 'contenido', 'created_at']);

        $data = $mensajes->map(fn ($m) => [
            'id'         => $m->id,
            'direccion'  => $m->direccion,
            'tipo'       => $m->tipo,
            'contenido'  => $m->contenido,
            'created_at' => $m->created_at?->toIso8601String(),
        ])->all();

        $asesor = $conv->asesor_id
            ? DB::table('web_asesor')->where('id', $conv->asesor_id)->first()
            : null;

        return response()->json([
            'data'          => $data,
            'phone'         => $conv->phone,
            'phone_display' => $conv->phone_display,
            'nombre'        => $conv->nombre,
            'estado'        => $conv->estado,
            'asesor_id'     => $conv->asesor_id,
            'asesor'        => $asesor,
        ]);
    }

    public function marcarAtendido(int $id): JsonResponse
    {
        $conv = WhatsappConversacion::findOrFail($id);
        $conv->update(['estado' => 'menu', 'contexto' => []]);

        return response()->json(['message' => 'Conversación marcada como atendida.']);
    }

    public function todosPhones(): JsonResponse
    {
        $phones = WhatsappConversacion::orderByDesc('updated_at')
            ->pluck('phone')
            ->unique()
            ->values();

        return response()->json(['phones' => $phones]);
    }

    private function nodeUrl(): string
    {
        return rtrim(env('WHATSAPP_SERVICE_URL', 'http://localhost:3001'), '/');
    }

    public function enviar(EnviarMensajeRequest $request): JsonResponse
    {
        try {
            $res = \Illuminate\Support\Facades\Http::timeout(10)
                ->post($this->nodeUrl() . '/enviar', [
                    'phone'   => $request->string('phone')->toString(),
                    'mensaje' => $request->string('mensaje')->toString(),
                ]);

            if ($res->successful()) {
                return response()->json(['message' => 'Mensaje enviado correctamente.']);
            }
            return response()->json(['message' => $res->json('message') ?? 'Error al enviar.'], 500);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'No se pudo conectar al bot: ' . $e->getMessage()], 503);
        }
    }

    public function enviarMasivo(EnviarMasivoRequest $request): JsonResponse
    {
        try {
            $res = \Illuminate\Support\Facades\Http::timeout(30)
                ->post($this->nodeUrl() . '/enviar-masivo', [
                    'phones'  => $request->phones,
                    'mensaje' => $request->string('mensaje')->toString(),
                ]);

            if ($res->successful()) {
                return response()->json($res->json());
            }
            return response()->json(['message' => $res->json('message') ?? 'Error al enviar masivo.'], 500);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'No se pudo conectar al bot: ' . $e->getMessage()], 503);
        }
    }

    public function enviarMedia(EnviarMediaRequest $request): JsonResponse
    {
        try {
            $archivo  = $request->file('archivo');
            $response = \Illuminate\Support\Facades\Http::timeout(60)
                ->attach('archivo', file_get_contents($archivo->getRealPath()), $archivo->getClientOriginalName())
                ->post($this->nodeUrl() . '/enviar-media', [
                    'phones'   => $request->phones,
                    'tipo'     => $request->get('tipo', 'document'),
                    'caption'  => $request->get('caption', ''),
                    'filename' => $request->get('filename', $archivo->getClientOriginalName()),
                ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }
            return response()->json(['message' => $response->json('message') ?? 'Error al enviar archivo.'], 500);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'No se pudo conectar al bot: ' . $e->getMessage()], 503);
        }
    }

    public function etiquetas(): JsonResponse
    {
        $etiquetas = WhatsappEtiqueta::orderBy('nombre')->get(['id', 'nombre', 'color']);

        return response()->json(['data' => $etiquetas]);
    }

    public function crearEtiqueta(StoreEtiquetaRequest $request): JsonResponse
    {
        $etiqueta = WhatsappEtiqueta::create([
            'nombre' => $request->string('nombre')->toString(),
            'color'  => $request->get('color', '#6366f1'),
        ]);

        return response()->json($etiqueta, 201);
    }

    public function actualizarEtiqueta(UpdateEtiquetaRequest $request, int $id): JsonResponse
    {
        $etiqueta = WhatsappEtiqueta::findOrFail($id);

        $etiqueta->update([
            'nombre' => $request->string('nombre')->toString(),
            'color'  => $request->get('color', $etiqueta->color),
        ]);

        return response()->json($etiqueta);
    }

    public function eliminarEtiqueta(int $id): JsonResponse
    {
        WhatsappEtiqueta::findOrFail($id)->delete();

        return response()->json(null, 204);
    }

    public function asignarEtiquetas(AsignarEtiquetasRequest $request, int $id): JsonResponse
    {
        $conv = WhatsappConversacion::findOrFail($id);
        $conv->etiquetas()->sync($request->etiqueta_ids);

        $etiquetas = $conv->etiquetas()->get(['whatsapp_etiquetas.id', 'nombre', 'color']);

        return response()->json(['etiquetas' => $etiquetas]);
    }

    public function enviarPlantilla(Request $request): JsonResponse
    {
        $request->validate([
            'phone'     => 'required|string',
            'plantilla' => 'required|in:confirmacion,entrega,promocion',
            'params'    => 'required|array',
        ]);

        $service = new WhatsAppService;
        $phone   = $request->phone;
        $params  = $request->params;

        try {
            $result = match ($request->plantilla) {
                'confirmacion' => $service->sendTemplate($phone, 'confirmacion_pedido', 'es', [
                    ['type' => 'text', 'text' => $params['numero_pedido'] ?? ''],
                    ['type' => 'text', 'text' => $params['total'] ?? ''],
                ]),
                'entrega' => $service->sendTemplate($phone, 'estado_entrega', 'es', [
                    ['type' => 'text', 'text' => $params['numero_pedido'] ?? ''],
                ]),
                'promocion' => $service->sendTemplate($phone, 'promocion_especial', 'es', [
                    ['type' => 'text', 'text' => $params['descuento'] ?? ''],
                    ['type' => 'text', 'text' => $params['fecha_fin'] ?? ''],
                ]),
            };

            return response()->json(['message' => 'Plantilla enviada correctamente.', 'result' => $result]);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Error al enviar la plantilla: '.$e->getMessage()], 500);
        }
    }
}
