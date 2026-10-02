<?php

namespace App\Application\Pagos\Handlers;

use App\Application\Pagos\Commands\UpdatePagoCommand;
use App\Application\Pagos\DTOs\PagoDTO;
use App\Domain\Pagos\Contracts\PagoRepositoryInterface;
use App\Domain\Pagos\Exceptions\PagoNotFoundException;
use Illuminate\Support\Facades\DB;

class UpdatePagoHandler
{
    public function __construct(
        private readonly PagoRepositoryInterface $repository,
    ) {}

    public function handle(UpdatePagoCommand $command): PagoDTO
    {
        $pago = $this->repository->findById($command->id);
        if (! $pago) {
            throw new PagoNotFoundException($command->id);
        }

        return DB::transaction(function () use ($command, $pago) {
            $this->repository->auditarCambio($pago, 'edicion', $command->idUsReg);

            $data = $command->data;
            if ($pago->estado_verificacion === 'observado') {
                $data['estado_verificacion'] = 'pendiente';
                $data['nota_verificacion']   = null;
            }

            $model = $this->repository->update($command->id, $data);
            return PagoDTO::fromModel($model);
        });
    }
}
