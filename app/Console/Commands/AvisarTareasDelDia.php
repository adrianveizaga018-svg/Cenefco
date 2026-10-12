<?php

namespace App\Console\Commands;

use App\Application\Crm\Services\CrmService;
use App\Enums\DestinatarioEnum;
use App\Enums\PrioridadEnum;
use App\Enums\TipoNotificacionEnum;
use App\Infrastructure\CampanasLeads\Models\Lead;
use App\Services\NotificacionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/** Cada mañana: a cada vendedor, cuántos seguimientos tiene hoy y cuántos se le pasaron. */
class AvisarTareasDelDia extends Command
{
    protected $signature = 'crm:tareas-del-dia';

    protected $description = 'Avisa a cada vendedor sus seguimientos del día';

    public function handle(NotificacionService $notificaciones): int
    {
        $pendientes = Lead::whereIn('estado', CrmService::ABIERTAS)
            ->whereNotNull('vendedor_asignado_id')
            ->where('proxima_accion_at', '<=', now()->endOfDay())
            ->get(['id', 'vendedor_asignado_id', 'proxima_accion_at']);

        $avisados = 0;
        foreach ($pendientes->groupBy('vendedor_asignado_id') as $vendedorId => $grupo) {
            $usuarioId = DB::table('vendedores')->where('id', $vendedorId)->value('usuario_id');
            if (! $usuarioId) {
                continue;
            }
            $vencidas = $grupo->filter(fn ($l) => $l->proxima_accion_at->lt(now()->startOfDay()))->count();

            $notificaciones->enviar(
                destinatario: DestinatarioEnum::USUARIO,
                tipo:         TipoNotificacionEnum::INTERESADO_SIN_ATENDER,
                titulo:       "Hoy tienes {$grupo->count()} seguimiento(s)",
                mensaje:      $vencidas ? "{$vencidas} vienen de días anteriores. Empieza por esos." : 'Ábrelos desde Mis tareas de hoy.',
                prioridad:    $vencidas ? PrioridadEnum::ALTA : PrioridadEnum::MEDIA,
                usuarioId:    (int) $usuarioId,
                urlAccion:    '/crm/tareas',
                icono:        'lucideListChecks',
                color:        $vencidas ? '#dc2626' : '#0b4258',
            );
            $avisados++;
        }

        $this->info("{$avisados} vendedor(es) avisados.");

        return self::SUCCESS;
    }
}
