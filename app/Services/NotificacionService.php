<?php

namespace App\Services;

use App\Enums\DestinatarioEnum;
use App\Enums\PrioridadEnum;
use App\Enums\TipoNotificacionEnum;
use App\Infrastructure\Notificaciones\Models\Notificacion;
use App\Infrastructure\Notificaciones\Models\NotificacionPreferencia;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class NotificacionService
{
    public function enviar(
        DestinatarioEnum    $destinatario,
        TipoNotificacionEnum|string $tipo,
        string              $titulo,
        string              $mensaje,
        PrioridadEnum       $prioridad   = PrioridadEnum::MEDIA,
        ?int                $usuarioId   = null,
        ?string             $rolDestino  = null,
        ?string             $urlAccion   = null,
        ?string             $icono       = null,
        ?string             $color       = null,
        ?string             $imagenUrl   = null,
        ?string             $imagenNombre = null,
        ?Carbon             $expiresAt   = null,
    ): void {
        $tipoStr = $tipo instanceof TipoNotificacionEnum ? $tipo->value : $tipo;

        $base = [
            'tipo'          => $tipoStr,
            'titulo'        => $titulo,
            'mensaje'       => $mensaje,
            'prioridad'     => $prioridad->value,
            'destinatario'  => $destinatario->value,
            'rol_destino'   => $rolDestino,
            'url_accion'    => $urlAccion,
            'icono'         => $icono,
            'color'         => $color,
            'imagen_url'    => $imagenUrl,
            'imagen_nombre' => $imagenNombre,
            'expires_at'    => $expiresAt,
            'canal'         => 'sistema',
            'leida'         => false,
            'enviada'       => true,
            'enviada_at'    => now(),
            'created_at'    => now(),
        ];

        match ($destinatario) {
            DestinatarioEnum::USUARIO => $this->enviarAUsuario($base, $usuarioId, $tipoStr, $prioridad),
            DestinatarioEnum::ROL     => $this->enviarARol($base, $rolDestino, $tipoStr, $prioridad),
            DestinatarioEnum::TODOS   => $this->enviarATodos($base, $tipoStr, $prioridad),
        };
    }

    public function enviarAPermiso(
        string              $permiso,
        TipoNotificacionEnum|string $tipo,
        string              $titulo,
        string              $mensaje,
        PrioridadEnum       $prioridad   = PrioridadEnum::MEDIA,
        ?string             $urlAccion   = null,
        ?string             $icono       = null,
        ?string             $color       = null,
    ): void {
        $tipoStr = $tipo instanceof TipoNotificacionEnum ? $tipo->value : $tipo;

        $base = [
            'tipo'          => $tipoStr,
            'titulo'        => $titulo,
            'mensaje'       => $mensaje,
            'prioridad'     => $prioridad->value,
            'destinatario'  => DestinatarioEnum::ROL->value,
            'rol_destino'   => $permiso,
            'url_accion'    => $urlAccion,
            'icono'         => $icono,
            'color'         => $color,
            'imagen_url'    => null,
            'imagen_nombre' => null,
            'expires_at'    => null,
            'canal'         => 'sistema',
            'leida'         => false,
            'enviada'       => true,
            'enviada_at'    => now(),
            'created_at'    => now(),
        ];

        $usuarioIds = DB::table('usuarios')
            ->join('usuarios_roles', 'usuarios.id', '=', 'usuarios_roles.usuario_id')
            ->join('roles_permisos', 'usuarios_roles.rol_id', '=', 'roles_permisos.rol_id')
            ->join('permisos', 'roles_permisos.permiso_id', '=', 'permisos.id')
            ->where('usuarios.activo', true)
            ->where(fn ($q) => $q->where('permisos.codigo', $permiso)->orWhere('permisos.codigo', '*'))
            ->distinct()
            ->pluck('usuarios.id');

        if ($usuarioIds->isEmpty()) {
            \Log::warning("[NotificacionService] enviarAPermiso('{$permiso}'): ningún usuario activo tiene ese permiso — notificación '{$titulo}' no llegó a nadie.");
            return;
        }

        $this->insertarEnBatch($base, $usuarioIds, $tipoStr, $prioridad);
    }

    private function enviarAUsuario(array $base, ?int $usuarioId, string $tipo, PrioridadEnum $prioridad): void
    {
        if (!$usuarioId) return;
        if ($prioridad !== PrioridadEnum::CRITICA && !$this->usuarioAceptaTipo($usuarioId, $tipo)) {
            return;
        }

        Notificacion::create(array_merge($base, ['usuario_id' => $usuarioId]));
    }

    private function enviarARol(array $base, ?string $rol, string $tipo, PrioridadEnum $prioridad): void
    {
        if (!$rol) return;

        $usuarioIds = DB::table('usuarios')
            ->join('usuarios_roles', 'usuarios.id', '=', 'usuarios_roles.usuario_id')
            ->join('roles', 'usuarios_roles.rol_id', '=', 'roles.id')
            ->where('roles.nombre', $rol)
            ->where('usuarios.activo', true)
            ->pluck('usuarios.id');

        if ($usuarioIds->isEmpty()) {
            \Log::warning("[NotificacionService] enviarARol('{$rol}'): ningún usuario activo pertenece a ese rol — notificación tipo '{$tipo}' no llegó a nadie.");
            return;
        }

        $this->insertarEnBatch($base, $usuarioIds, $tipo, $prioridad);
    }

    private function enviarATodos(array $base, string $tipo, PrioridadEnum $prioridad): void
    {
        $usuarioIds = DB::table('usuarios')->where('activo', true)->pluck('id');

        $this->insertarEnBatch($base, $usuarioIds, $tipo, $prioridad);
    }

    private function insertarEnBatch(array $base, Collection $usuarioIds, string $tipo, PrioridadEnum $prioridad): void
    {
        $registros = [];
        $ahora     = now()->toDateTimeString();

        foreach ($usuarioIds as $uid) {
            if ($prioridad !== PrioridadEnum::CRITICA && !$this->usuarioAceptaTipo($uid, $tipo)) {
                continue;
            }
            $registros[] = array_merge($base, [
                'usuario_id'  => $uid,
                'expires_at'  => $base['expires_at'] instanceof Carbon ? $base['expires_at']->toDateTimeString() : $base['expires_at'],
                'enviada_at'  => $ahora,
                'created_at'  => $ahora,
            ]);
        }

        foreach (array_chunk($registros, 500) as $lote) {
            Notificacion::insert($lote);
        }
    }

    private function usuarioAceptaTipo(int $usuarioId, string $tipo): bool
    {
        return !NotificacionPreferencia::where('usuario_id', $usuarioId)
            ->where('tipo', $tipo)
            ->where('activa', false)
            ->exists();
    }
}
