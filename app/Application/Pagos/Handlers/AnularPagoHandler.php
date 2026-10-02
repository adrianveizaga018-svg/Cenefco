<?php

namespace App\Application\Pagos\Handlers;

use App\Application\Pagos\Commands\AnularPagoCommand;
use App\Domain\Pagos\Contracts\PagoRepositoryInterface;
use App\Domain\Pagos\Exceptions\PagoNotFoundException;
use Illuminate\Support\Facades\DB;

class AnularPagoHandler
{
    public function __construct(
        private readonly PagoRepositoryInterface $repository,
    ) {}

    public function handle(AnularPagoCommand $command): void
    {
        $pago = $this->repository->findById($command->id);
        if (! $pago) {
            throw new PagoNotFoundException($command->id);
        }

        DB::transaction(function () use ($command, $pago) {
            $this->repository->auditarCambio($pago, 'anulacion', $command->idUsReg);
            $this->repository->anular($command->id, $command->idUsReg);
        });
    }
}
