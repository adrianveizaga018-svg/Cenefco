<?php

namespace App\Application\CompromisosCobro\Handlers;

use App\Application\CompromisosCobro\Commands\CreateCompromisoCobroCommand;
use App\Application\CompromisosCobro\DTOs\CompromisoCobroDTO;
use App\Application\CompromisosCobro\Services\VendedorDeInscripcionResolver;
use App\Domain\CompromisosCobro\Contracts\CompromisoCobroRepositoryInterface;
use App\Domain\CompromisosCobro\Enums\EstadoCompromisoCobro;
use App\Domain\CompromisosCobro\Exceptions\CompromisoCobroYaExisteException;
use App\Enums\DestinatarioEnum;
use App\Enums\PrioridadEnum;
use App\Enums\TipoNotificacionEnum;
use App\Services\NotificacionService;
use Illuminate\Support\Facades\DB;

class CreateCompromisoCobroHandler
{
    public function __construct(
        private readonly CompromisoCobroRepositoryInterface $repository,
        private readonly VendedorDeInscripcionResolver      $vendedorResolver,
        private readonly NotificacionService                $notificacionService,
    ) {}

    public function handle(CreateCompromisoCobroCommand $c): CompromisoCobroDTO
    {
        $dto = DB::transaction(function () use ($c) {

            if ($this->repository->existeAbiertoParaConLock($c->idIns)) {
                throw new CompromisoCobroYaExisteException($c->idIns);
            }

            $nuevo = $this->repository->create([
                'id_ins'             => $c->idIns,
                'id_us'              => $c->idUs,
                'id_imp'             => $c->idImp,
                'fecha_compromiso'   => $c->fechaCompromiso,
                'hora_compromiso'    => $c->horaCompromiso,
                'monto_comprometido' => $c->montoComprometido,
                'observacion'        => $c->observacion,
                'estado'             => EstadoCompromisoCobro::Pendiente->value,
                'registrado_por'     => $c->registradoPor,
            ]);

            $this->repository->registrarLog($nuevo->id, [
                'tipo_evento'    => 'creado',
                'fecha_nueva'    => $c->fechaCompromiso,
                'observacion'    => $c->observacion,
                'registrado_por' => $c->registradoPor,
            ]);

            return $nuevo;
        });

        $this->notificarVendedorOAdmin($dto);

        return $dto;
    }

    private function notificarVendedorOAdmin(CompromisoCobroDTO $dto): void
    {
        $usuarioVendedorId = $this->vendedorResolver->resolverUsuarioId($dto->id_ins);

        $titulo  = 'Compromiso de cobro registrado';
        $mensaje = ($dto->estudiante_nombre ?: 'El estudiante') . " se comprometió a pagar el {$dto->fecha_compromiso}"
            . ($dto->hora_compromiso ? " a las {$dto->hora_compromiso}" : '')
            . ($dto->monto_comprometido ? " (Bs. {$dto->monto_comprometido})" : '') . '.'
            . ($dto->curso_nombre ? " Curso: {$dto->curso_nombre}." : '');
        $urlAccion = "/cenefco/inscripcion-detail/{$dto->id_ins}";

        if ($usuarioVendedorId) {
            $this->notificacionService->enviar(
                destinatario: DestinatarioEnum::USUARIO,
                tipo:         TipoNotificacionEnum::COMPROMISO_COBRO,
                titulo:       $titulo,
                mensaje:      $mensaje,
                prioridad:    PrioridadEnum::MEDIA,
                usuarioId:    $usuarioVendedorId,
                urlAccion:    $urlAccion,
                icono:        'lucideCalendarClock',
                color:        '#f59e0b',
            );
        } else {
            $this->notificacionService->enviarAPermiso(
                permiso:      'compromisos-cobro.editar',
                tipo:         TipoNotificacionEnum::COMPROMISO_COBRO,
                titulo:       $titulo,
                mensaje:      $mensaje,
                prioridad:    PrioridadEnum::MEDIA,
                urlAccion:    $urlAccion,
                icono:        'lucideCalendarClock',
                color:        '#f59e0b',
            );
        }
    }
}
