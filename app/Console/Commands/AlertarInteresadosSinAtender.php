<?php

namespace App\Console\Commands;

use App\Application\Crm\Services\CrmService;
use App\Enums\DestinatarioEnum;
use App\Enums\PrioridadEnum;
use App\Enums\TipoNotificacionEnum;
use App\Services\NotificacionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Avisa a cada vendedor de sus interesados con más de 24 horas sin contacto, y a
 * Dirección un resumen. Cada interesado se avisa una vez; un nuevo contacto lo rearma.
 */
class AlertarInteresadosSinAtender extends Command
{
    protected $signature = 'crm:alertas';

    protected $description = 'Avisa los interesados del embudo que llevan más de 24 horas sin atención';

    public function handle(CrmService $crm, NotificacionService $notificaciones): int
    {
        if (now()->isSunday()) {
            return self::SUCCESS; // solo días hábiles
        }

        $pendientes = $crm->sinAtender()->whereNull('alerta_enviada_at')->get(['id', 'nombre', 'vendedor_asignado_id']);
        if ($pendientes->isEmpty()) {
            $this->info('Sin interesados nuevos por avisar.');

            return self::SUCCESS;
        }

        foreach ($pendientes->groupBy('vendedor_asignado_id') as $vendedorId => $grupo) {
            $usuarioId = $vendedorId ? DB::table('vendedores')->where('id', $vendedorId)->value('usuario_id') : null;
            if (! $usuarioId) {
                continue;
            }
            $nombres = $grupo->take(3)->pluck('nombre')->implode(', ').($grupo->count() > 3 ? ' y '.($grupo->count() - 3).' más' : '');
            $notificaciones->enviar(
                destinatario: DestinatarioEnum::USUARIO,
                tipo:         TipoNotificacionEnum::INTERESADO_SIN_ATENDER,
                titulo:       $grupo->count().' interesado(s) sin atender hace más de '.CrmService::HORAS_SIN_ATENCION.' h',
                mensaje:      "Contacta hoy a: {$nombres}.",
                prioridad:    PrioridadEnum::ALTA,
                usuarioId:    (int) $usuarioId,
                urlAccion:    '/crm/embudo',
                icono:        'lucideAlarmClock',
                color:        '#dc2626',
            );
        }

        $notificaciones->enviarAPermiso(
            permiso:   'crm.supervisar',
            tipo:      TipoNotificacionEnum::INTERESADO_SIN_ATENDER,
            titulo:    $pendientes->count().' interesado(s) pasaron '.CrmService::HORAS_SIN_ATENCION.' h sin atención',
            mensaje:   'Revisa el tablero de ventas para ver de qué vendedores son o reasignarlos.',
            prioridad: PrioridadEnum::MEDIA,
            urlAccion: '/crm/tablero',
            icono:     'lucideAlarmClock',
            color:     '#f59e0b',
        );

        DB::table('leads')->whereIn('id', $pendientes->pluck('id'))->update(['alerta_enviada_at' => now()]);
        $this->info($pendientes->count().' interesado(s) avisados.');

        return self::SUCCESS;
    }
}
