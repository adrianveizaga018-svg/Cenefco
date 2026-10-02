<?php

namespace App\Application\Pagos\Handlers;

use App\Application\Pagos\Commands\VerificarPagoCommand;
use App\Application\Pagos\DTOs\PagoDTO;
use App\Domain\Pagos\Contracts\PagoRepositoryInterface;
use App\Domain\Pagos\Exceptions\PagoNotFoundException;
use Illuminate\Support\Facades\DB;

class VerificarPagoHandler
{
    public function __construct(
        private readonly PagoRepositoryInterface $repository,
    ) {}

    public function handle(VerificarPagoCommand $command): PagoDTO
    {
        $pago = $this->repository->findById($command->id);
        if (! $pago) {
            throw new PagoNotFoundException($command->id);
        }

        return DB::transaction(function () use ($command, $pago) {
            $this->repository->auditarCambio($pago, 'verificacion', $command->idUsReg);
            $model = $this->repository->update($command->id, ['estado_verificacion' => 'verificado']);

            return PagoDTO::fromModel($model);
        });
    }
}
