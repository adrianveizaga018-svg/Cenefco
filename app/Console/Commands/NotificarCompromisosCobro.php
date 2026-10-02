<?php

namespace App\Console\Commands;

use App\Application\CompromisosCobro\Services\VendedorDeInscripcionResolver;
use App\Domain\CompromisosCobro\Contracts\CompromisoCobroRepositoryInterface;
use App\Enums\DestinatarioEnum;
use App\Enums\PrioridadEnum;
use App\Enums\TipoNotificacionEnum;
use App\Services\NotificacionService;
use Illuminate\Console\Command;

class NotificarCompromisosCobro extends Command
{
    protected $signature   = 'compromisos-cobro:recordatorios';
    protected $description = 'Notifica al vendedor los compromisos de cobro que vencen hoy, y escala los que vencieron sin pago';

    public function handle(
        CompromisoCobroRepositoryInterface $repository,
        VendedorDeInscripcionResolver      $vendedorResolver,
        NotificacionService                $notificacionService,
    ): int {
        $hoy = $repository->pendientesConFechaHoySinNotificar();

        $this->info("Compromisos con fecha de hoy sin notificar: {$hoy->count()}");

        foreach ($hoy as $compromiso) {
            $usuarioVendedorId = $vendedorResolver->resolverUsuarioId($compromiso->id_ins);
            $urlAccion = "/cenefco/inscripcion-detail/{$compromiso->id_ins}";
            $mensaje   = ($compromiso->estudiante_nombre ?: 'El estudiante') . ' se comprometió a pagar hoy'
                . ($compromiso->hora_compromiso ? " a las {$compromiso->hora_compromiso}" : '')
                . ($compromiso->monto_comprometido ? " (Bs. {$compromiso->monto_comprometido})" : '') . '.'
                . ($compromiso->curso_nombre ? " Curso: {$compromiso->curso_nombre}." : '');

            if ($usuarioVendedorId) {
                $notificacionService->enviar(
                    destinatario: DestinatarioEnum::USUARIO,
                    tipo:         TipoNotificacionEnum::COMPROMISO_COBRO,
                    titulo:       'Hoy vence un compromiso de cobro',
                    mensaje:      $mensaje,
                    prioridad:    PrioridadEnum::ALTA,
                    usuarioId:    $usuarioVendedorId,
                    urlAccion:    $urlAccion,
                    icono:        'lucideCalendarClock',
                    color:        '#f59e0b',
                );
            } else {
                $notificacionService->enviarAPermiso(
                    permiso:      'compromisos-cobro.editar',
                    tipo:         TipoNotificacionEnum::COMPROMISO_COBRO,
                    titulo:       'Hoy vence un compromiso de cobro',
                    mensaje:      $mensaje,
                    prioridad:    PrioridadEnum::ALTA,
                    urlAccion:    $urlAccion,
                    icono:        'lucideCalendarClock',
                    color:        '#f59e0b',
                );
            }

            $repository->update($compromiso->id, ['notificado_at' => now()]);
        }

        $vencidos = $repository->pendientesVencidosSinNotificar();

        $this->info("Compromisos vencidos sin notificar: {$vencidos->count()}");

        foreach ($vencidos as $compromiso) {
            $usuarioVendedorId = $vendedorResolver->resolverUsuarioId($compromiso->id_ins);
            $urlAccion = "/cenefco/inscripcion-detail/{$compromiso->id_ins}";
            $mensaje   = "El compromiso de cobro de " . ($compromiso->estudiante_nombre ?: 'un estudiante')
                . " del {$compromiso->fecha_compromiso}"
                . ($compromiso->hora_compromiso ? " ({$compromiso->hora_compromiso})" : '')
                . ' venció sin registrarse el pago.'
                . ($compromiso->curso_nombre ? " Curso: {$compromiso->curso_nombre}." : '');

            if ($usuarioVendedorId) {
                $notificacionService->enviar(
                    destinatario: DestinatarioEnum::USUARIO,
                    tipo:         TipoNotificacionEnum::COMPROMISO_COBRO_VENCIDO,
                    titulo:       'Compromiso de cobro vencido',
                    mensaje:      $mensaje,
                    prioridad:    PrioridadEnum::ALTA,
                    usuarioId:    $usuarioVendedorId,
                    urlAccion:    $urlAccion,
                    icono:        'lucideAlertTriangle',
                    color:        '#dc2626',
                );
            } else {
                $notificacionService->enviarAPermiso(
                    permiso:      'compromisos-cobro.editar',
                    tipo:         TipoNotificacionEnum::COMPROMISO_COBRO_VENCIDO,
                    titulo:       'Compromiso de cobro vencido',
                    mensaje:      $mensaje,
                    prioridad:    PrioridadEnum::ALTA,
                    urlAccion:    $urlAccion,
                    icono:        'lucideAlertTriangle',
                    color:        '#dc2626',
                );
            }

            $repository->update($compromiso->id, [
                'estado'                => 'incumplido',
                'vencido_notificado_at' => now(),
            ]);
        }

        return self::SUCCESS;
    }
}
