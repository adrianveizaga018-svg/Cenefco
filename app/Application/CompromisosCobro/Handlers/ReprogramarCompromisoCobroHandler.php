<?php

namespace App\Application\CompromisosCobro\Handlers;

use App\Application\CompromisosCobro\Commands\ReprogramarCompromisoCobroCommand;
use App\Application\CompromisosCobro\DTOs\CompromisoCobroDTO;
use App\Application\CompromisosCobro\Services\VendedorDeInscripcionResolver;
use App\Domain\CompromisosCobro\Contracts\CompromisoCobroRepositoryInterface;
use App\Domain\CompromisosCobro\Enums\EstadoCompromisoCobro;
use App\Enums\DestinatarioEnum;
use App\Enums\PrioridadEnum;
use App\Enums\TipoNotificacionEnum;
use App\Services\NotificacionService;
use Illuminate\Support\Facades\DB;

class ReprogramarCompromisoCobroHandler
{
    private const UMBRAL_ESCALAMIENTO = 3;

    public function __construct(
        private readonly CompromisoCobroRepositoryInterface $repository,
        private readonly VendedorDeInscripcionResolver      $vendedorResolver,
        private readonly NotificacionService                $notificacionService,
    ) {}

    public function handle(ReprogramarCompromisoCobroCommand $c): CompromisoCobroDTO
    {
        $dto = DB::transaction(function () use ($c) {
            $actual = $this->repository->findById($c->id);

            $this->repository->registrarLog($c->id, [
                'tipo_evento'    => 'reprogramado',
                'fecha_anterior' => $actual->fecha_compromiso,
                'fecha_nueva'    => $c->nuevaFecha,
                'motivo'         => $c->motivo->value,
                'observacion'    => $c->observacion,
                'registrado_por' => $c->registradoPor,
            ]);

            return $this->repository->update($c->id, [
                'fecha_compromiso'      => $c->nuevaFecha,
                'hora_compromiso'       => $c->nuevaHora,
                'estado'                => EstadoCompromisoCobro::Pendiente->value,
                'veces_reprogramado'    => $actual->veces_reprogramado + 1,
                'notificado_at'         => null,
                'vencido_notificado_at' => null,
            ]);
        });

        $this->notificarReprogramacion($dto);

        return $dto;
    }

    private function notificarReprogramacion(CompromisoCobroDTO $dto): void
    {
        $usuarioVendedorId = $this->vendedorResolver->resolverUsuarioId($dto->id_ins);
        $urlAccion = "/cenefco/inscripcion-detail/{$dto->id_ins}";
        $mensaje   = "Se reprogramó el compromiso de cobro de " . ($dto->estudiante_nombre ?: 'un estudiante')
            . " para el {$dto->fecha_compromiso}"
            . ($dto->hora_compromiso ? " a las {$dto->hora_compromiso}" : '') . '.'
            . ($dto->curso_nombre ? " Curso: {$dto->curso_nombre}." : '');

        if ($usuarioVendedorId) {
            $this->notificacionService->enviar(
                destinatario: DestinatarioEnum::USUARIO,
                tipo:         TipoNotificacionEnum::COMPROMISO_COBRO,
                titulo:       'Compromiso de cobro reprogramado',
                mensaje:      $mensaje,
                prioridad:    PrioridadEnum::MEDIA,
                usuarioId:    $usuarioVendedorId,
                urlAccion:    $urlAccion,
                icono:        'lucideCalendarClock',
                color:        '#f59e0b',
            );
        }

        if ($dto->veces_reprogramado >= self::UMBRAL_ESCALAMIENTO) {
            $this->notificacionService->enviarAPermiso(
                permiso:      'compromisos-cobro.editar',
                tipo:         TipoNotificacionEnum::COMPROMISO_COBRO_VENCIDO,
                titulo:       'Compromiso de cobro reprogramado varias veces',
                mensaje:      "Van {$dto->veces_reprogramado} reprogramaciones sin que el estudiante pague. " . $mensaje,
                prioridad:    PrioridadEnum::ALTA,
                urlAccion:    $urlAccion,
                icono:        'lucideAlertTriangle',
                color:        '#dc2626',
            );
        }
    }
}
