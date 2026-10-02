<?php

namespace App\Application\Pagos\Handlers;

use App\Application\Pagos\Commands\ObservarPagoCommand;
use App\Application\Pagos\DTOs\PagoDTO;
use App\Domain\Pagos\Contracts\PagoRepositoryInterface;
use App\Domain\Pagos\Exceptions\PagoNotFoundException;
use App\Enums\DestinatarioEnum;
use App\Enums\PrioridadEnum;
use App\Enums\TipoNotificacionEnum;
use App\Services\NotificacionService;
use Illuminate\Support\Facades\DB;

class ObservarPagoHandler
{
    public function __construct(
        private readonly PagoRepositoryInterface $repository,
        private readonly NotificacionService $notificacionService,
    ) {}

    public function handle(ObservarPagoCommand $command): PagoDTO
    {
        $pago = $this->repository->findById($command->id);
        if (! $pago) {
            throw new PagoNotFoundException($command->id);
        }

        $model = DB::transaction(function () use ($command, $pago) {
            $this->repository->auditarCambio($pago, 'observacion', $command->idUsReg);

            return $this->repository->update($command->id, [
                'estado_verificacion' => 'observado',
                'nota_verificacion'   => $command->nota,
            ]);
        });

        if ($pago->id_us_cajero) {
            $this->notificacionService->enviar(
                destinatario: DestinatarioEnum::USUARIO,
                tipo:         TipoNotificacionEnum::PAGO_OBSERVADO,
                titulo:       'Pago observado — requiere corrección',
                mensaje:      "Un pago de Bs. {$pago->monto_pagado} que registraste fue observado: \"{$command->nota}\". Corrígelo para que vuelva a revisión.",
                prioridad:    PrioridadEnum::ALTA,
                usuarioId:    (int) $pago->id_us_cajero,
                urlAccion:    "/cenefco/pago-edit/{$pago->id_pago}",
                icono:        'lucideAlertTriangle',
                color:        '#ef4444',
            );
        }

        return PagoDTO::fromModel($model);
    }
}
