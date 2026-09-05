<?php

namespace App\Application\Pagos\Handlers;

use App\Application\Pagos\Commands\CreatePagoCommand;
use App\Application\Pagos\DTOs\PagoDTO;
use App\Domain\CompromisosCobro\Contracts\CompromisoCobroRepositoryInterface;
use App\Domain\Pagos\Contracts\PagoRepositoryInterface;
use App\Domain\Pagos\Exceptions\PagoBoletaDuplicadaException;
use App\Domain\Pagos\Exceptions\PagoDuplicadoException;
use App\Enums\PrioridadEnum;
use App\Enums\TipoNotificacionEnum;
use App\Services\NotificacionService;
use Illuminate\Support\Facades\DB;

class CreatePagoHandler
{
    public function __construct(
        private readonly PagoRepositoryInterface $repository,
        private readonly CompromisoCobroRepositoryInterface $compromisoCobroRepository,
        private readonly NotificacionService $notificacionService,
    ) {}

    public function handle(CreatePagoCommand $command): PagoDTO
    {
        $model = DB::transaction(function () use ($command) {

            if ($command->idFechapago !== null) {
                if ($this->repository->existePagoActivoConLock($command->idUs, $command->idFechapago)) {
                    throw new PagoDuplicadoException();
                }
            }

            if ($command->nroBoleta !== null && trim($command->nroBoleta) !== '') {
                if ($this->repository->existePorBoletaConLock($command->nroBoleta)) {
                    throw new PagoBoletaDuplicadaException();
                }
            }

            $pago = $this->repository->create([
                'id_us_reg'           => $command->idUsReg,
                'id_us'               => $command->idUs,
                'id_mat'              => $command->idMat,
                'id_fechapago'        => $command->idFechapago,
                'id_ins'              => $command->idIns,
                'monto_pagado'        => $command->montoPagado,
                'nro_boleta_bancaria' => $command->nroBoleta,
                'fecha_deposito'      => $command->fechaDeposito ?? now()->toDateString(),
                'nro_nit'             => $command->nroNit,
                'nombre_nit'          => $command->nombreNit,
                'tipo_fechapago'      => $command->tipoFechapago,
                'observacion_pago'    => $command->observacion,
                'metodo_pago'         => $command->metodoPago,
                'id_us_cajero'        => $command->idUsCajero,
                'comprobante_archivo' => $command->comprobanteArchivo,
                'tipo_banco_id'       => $command->tipoBancoId,
                'pago_extra'          => 0,
                'estado'              => 1,
                'estado_verificacion' => 'pendiente',
                'fecha_reg'           => now(),
                'monto_descuento_extra' => $command->montoDescuento,
                'motivo_descuento'      => $command->motivoDescuento,
            ]);

            if ($pago->id_ins) {
                $this->compromisoCobroRepository->marcarCumplidosDe(
                    $pago->id_ins,
                    $command->idUsCajero ?? $command->idUsReg,
                );
            }

            return $pago;
        });

        $mensajeDescuento = $command->montoDescuento
            ? " Incluye descuento de Bs. {$command->montoDescuento} ({$command->motivoDescuento})."
            : '';

        $this->notificacionService->enviarAPermiso(
            permiso:      'pagos.editar',
            tipo:         TipoNotificacionEnum::PAGO_REGISTRADO,
            titulo:       'Nuevo pago registrado — pendiente de revisión',
            mensaje:      "Se registró un pago de Bs. {$command->montoPagado} (boleta: " . ($command->nroBoleta ?? 's/n') . ").{$mensajeDescuento} Revisa el comprobante y valídalo.",
            prioridad:    PrioridadEnum::MEDIA,
            urlAccion:    "/cenefco/pago-edit/{$model->id_pago}",
            icono:        'lucideBanknote',
            color:        '#f59e0b',
        );

        return PagoDTO::fromModel($model);
    }
}
