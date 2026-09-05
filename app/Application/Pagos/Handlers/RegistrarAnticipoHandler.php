<?php

namespace App\Application\Pagos\Handlers;

use App\Application\Pagos\Commands\RegistrarAnticipoCommand;
use App\Application\Pagos\DTOs\PagoDTO;
use App\Domain\CompromisosCobro\Contracts\CompromisoCobroRepositoryInterface;
use App\Domain\Pagos\Contracts\PagoRepositoryInterface;
use App\Enums\PrioridadEnum;
use App\Enums\TipoNotificacionEnum;
use App\Services\NotificacionService;
use Illuminate\Support\Facades\DB;

class RegistrarAnticipoHandler
{
    public function __construct(
        private readonly PagoRepositoryInterface $repository,
        private readonly CompromisoCobroRepositoryInterface $compromisoCobroRepository,
        private readonly NotificacionService $notificacionService,
    ) {}

    public function handle(RegistrarAnticipoCommand $command): PagoDTO
    {
        $model = DB::transaction(function () use ($command) {
            $pago = $this->repository->create([
                'id_us_reg'           => $command->idUsReg,
                'id_us'               => $command->idUs,
                'id_fechapago'        => null,
                'id_ins'              => $command->idIns,
                'monto_pagado'        => $command->montoPagado,
                'id_us_cajero'        => $command->idUsCajero,
                'nro_boleta_bancaria' => $command->nroBoleta,
                'fecha_deposito'      => $command->fechaDeposito ?? now()->toDateString(),
                'observacion_pago'    => $command->observacion,
                'pago_extra'          => 1,
                'estado'              => 1,
                'estado_verificacion' => 'pendiente',
                'fecha_reg'           => now(),
            ]);

            if ($pago->id_ins) {
                $this->compromisoCobroRepository->marcarCumplidosDe(
                    $pago->id_ins,
                    $command->idUsCajero ?? $command->idUsReg,
                );
            }

            return $pago;
        });

        $this->notificacionService->enviarAPermiso(
            permiso:      'pagos.editar',
            tipo:         TipoNotificacionEnum::PAGO_REGISTRADO,
            titulo:       'Nuevo anticipo registrado — pendiente de revisión',
            mensaje:      "Se registró un anticipo de Bs. {$command->montoPagado} (boleta: " . ($command->nroBoleta ?? 's/n') . '). Revisa el comprobante y valídalo.',
            prioridad:    PrioridadEnum::MEDIA,
            urlAccion:    "/cenefco/pago-edit/{$model->id_pago}",
            icono:        'lucideBanknote',
            color:        '#f59e0b',
        );

        return PagoDTO::fromModel($model);
    }
}
