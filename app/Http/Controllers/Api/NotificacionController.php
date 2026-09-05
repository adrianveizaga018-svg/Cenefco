<?php

namespace App\Http\Controllers\Api;

use App\Enums\DestinatarioEnum;
use App\Enums\PrioridadEnum;
use App\Enums\TipoNotificacionEnum;
use App\Http\Controllers\Controller;
use App\Infrastructure\Notificaciones\Models\Notificacion;
use App\Infrastructure\Notificaciones\Models\NotificacionPreferencia;
use App\Services\NotificacionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NotificacionController extends Controller
{
    public function __construct(private readonly NotificacionService $service) {}

    public function index(Request $request): JsonResponse
    {
        $size = (int) $request->get('pageSize', 20);
        $page = (int) $request->get('pageIndex', 1);

        $q = Notificacion::where('usuario_id', $request->user()->id)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));

        if ($request->filled('leida')) {
            $q->where('leida', $request->boolean('leida'));
        }
        if ($request->filled('prioridad')) {
            $q->where('prioridad', $request->get('prioridad'));
        }
        if ($request->filled('tipo')) {
            $q->where('tipo', $request->get('tipo'));
        }
        if ($request->filled('desde')) {
            $q->where('created_at', '>=', $request->get('desde'));
        }
        if ($request->filled('hasta')) {
            $q->where('created_at', '<=', $request->get('hasta'));
        }

        $total = $q->count();
        $data  = $q->orderByDesc('created_at')->offset(($page - 1) * $size)->limit($size)->get();

        return response()->json(['data' => $data, 'total' => $total]);
    }

    public function noLeidas(Request $request): JsonResponse
    {
        $uid = $request->user()->id;

        $count = Notificacion::where('usuario_id', $uid)
            ->where('leida', false)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->count();

        $items = Notificacion::where('usuario_id', $uid)
            ->where('leida', false)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return response()->json(['count' => $count, 'items' => $items]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $notif = Notificacion::where('usuario_id', $request->user()->id)->findOrFail($id);

        if (!$notif->leida) {
            $notif->update(['leida' => true, 'leida_at' => now()]);
        }

        return response()->json(['data' => $notif]);
    }

    public function marcarLeida(Request $request, int $id): JsonResponse
    {
        $notif = Notificacion::where('usuario_id', $request->user()->id)->findOrFail($id);
        $notif->update(['leida' => true, 'leida_at' => now()]);

        return response()->json(['data' => $notif]);
    }

    public function marcarTodasLeidas(Request $request): JsonResponse
    {
        $count = Notificacion::where('usuario_id', $request->user()->id)
            ->where('leida', false)
            ->update(['leida' => true, 'leida_at' => now()]);

        return response()->json(['marcadas' => $count]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $notif = Notificacion::where('usuario_id', $request->user()->id)->findOrFail($id);
        $notif->delete();

        return response()->json(null, 204);
    }

    public function comunicado(Request $request): JsonResponse
    {
        $data = $request->validate([
            'titulo'        => ['required', 'string', 'max:120'],
            'mensaje'       => ['required', 'string', 'max:2000'],
            'tipo'          => ['nullable', 'in:comunicado,actividad'],
            'prioridad'     => ['required', 'in:baja,media,alta,critica'],
            'destinatario'  => ['required', 'in:usuario,rol,todos'],
            'rol_destino'   => ['required_if:destinatario,rol', 'nullable', 'string'],
            'usuario_ids'   => ['required_if:destinatario,usuario', 'nullable', 'array'],
            'usuario_ids.*' => ['integer', 'exists:usuarios,id'],
            'expires_at'    => ['nullable', 'date', 'after:now'],
            'imagen'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);

        $tipo = TipoNotificacionEnum::from($data['tipo'] ?? 'comunicado');

        $imagenUrl    = null;
        $imagenNombre = null;
        if ($request->hasFile('imagen')) {
            $file         = $request->file('imagen');
            $path         = $file->store('notificaciones', 'public');
            $imagenUrl    = Storage::url($path);
            $imagenNombre = $file->getClientOriginalName();
        }

        $destinatario = DestinatarioEnum::from($data['destinatario']);
        $prioridad    = PrioridadEnum::from($data['prioridad']);
        $expiresAt    = isset($data['expires_at']) ? \Carbon\Carbon::parse($data['expires_at']) : null;

        if ($destinatario === DestinatarioEnum::USUARIO && !empty($data['usuario_ids'])) {
            foreach ($data['usuario_ids'] as $uid) {
                $this->service->enviar(
                    destinatario:  DestinatarioEnum::USUARIO,
                    tipo:          $tipo,
                    titulo:        $data['titulo'],
                    mensaje:       $data['mensaje'],
                    prioridad:     $prioridad,
                    usuarioId:     $uid,
                    imagenUrl:     $imagenUrl,
                    imagenNombre:  $imagenNombre,
                    expiresAt:     $expiresAt,
                );
            }
        } else {
            $this->service->enviar(
                destinatario:  $destinatario,
                tipo:          $tipo,
                titulo:        $data['titulo'],
                mensaje:       $data['mensaje'],
                prioridad:     $prioridad,
                rolDestino:    $data['rol_destino'] ?? null,
                imagenUrl:     $imagenUrl,
                imagenNombre:  $imagenNombre,
                expiresAt:     $expiresAt,
            );
        }

        return response()->json(['ok' => true], 201);
    }

    public function enviados(Request $request): JsonResponse
    {
        $size = (int) $request->get('pageSize', 20);
        $page = (int) $request->get('pageIndex', 1);

        $q = Notificacion::whereIn('tipo', [TipoNotificacionEnum::COMUNICADO->value, TipoNotificacionEnum::ACTIVIDAD->value])
            ->groupBy('tipo', 'titulo', 'mensaje', 'imagen_url', 'imagen_nombre', 'destinatario', 'rol_destino', 'created_at', 'expires_at')
            ->selectRaw('tipo, titulo, mensaje, imagen_url, imagen_nombre, destinatario, rol_destino, expires_at, created_at, COUNT(*) as total_enviadas, SUM(CASE WHEN leida THEN 1 ELSE 0 END) as total_leidas');

        $total = $q->count();
        $data  = $q->orderByDesc('created_at')->offset(($page - 1) * $size)->limit($size)->get();

        return response()->json(['data' => $data, 'total' => $total]);
    }

    public function preferencias(Request $request): JsonResponse
    {
        $prefs = NotificacionPreferencia::where('usuario_id', $request->user()->id)->get();
        return response()->json(['data' => $prefs]);
    }

    public function guardarPreferencias(Request $request): JsonResponse
    {
        $data = $request->validate([
            'preferencias'        => ['required', 'array'],
            'preferencias.*.tipo' => ['required', 'string'],
            'preferencias.*.activa' => ['required', 'boolean'],
        ]);

        foreach ($data['preferencias'] as $pref) {
            NotificacionPreferencia::updateOrCreate(
                ['usuario_id' => $request->user()->id, 'tipo' => $pref['tipo']],
                ['activa' => $pref['activa']]
            );
        }

        return response()->json(['ok' => true]);
    }

    public function stream(Request $request): StreamedResponse
    {
        $uid = $request->user()->id;

        $afterId = $request->integer('after_id');
        $lastId  = $afterId > 0
            ? $afterId
            : (int) (Notificacion::where('usuario_id', $uid)->max('id') ?? 0);

        return response()->stream(function () use ($uid, $lastId) {
            $maxIter = 60;
            $iter    = 0;

            while ($iter++ < $maxIter) {
                if (connection_aborted()) break;

                $nuevas = Notificacion::where('usuario_id', $uid)
                    ->where('id', '>', $lastId)
                    ->where('leida', false)
                    ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
                    ->orderBy('id')
                    ->get();

                if ($nuevas->isNotEmpty()) {
                    $lastId = $nuevas->last()->id;
                    echo 'data: ' . $nuevas->toJson() . "\n\n";
                    ob_flush();
                    flush();
                }

                sleep(5);
            }

            echo "event: close\ndata: reconnect\n\n";
            ob_flush();
            flush();
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Connection'        => 'keep-alive',
        ]);
    }
}
