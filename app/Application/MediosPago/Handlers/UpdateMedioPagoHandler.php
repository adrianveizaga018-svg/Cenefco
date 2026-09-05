<?php

namespace App\Application\MediosPago\Handlers;

use App\Application\MediosPago\Commands\UpdateMedioPagoCommand;
use App\Application\MediosPago\DTOs\MedioPagoDTO;
use App\Domain\MediosPago\Contracts\MedioPagoRepositoryInterface;

class UpdateMedioPagoHandler
{
    public function __construct(private readonly MedioPagoRepositoryInterface $repository) {}

    public function handle(UpdateMedioPagoCommand $command): MedioPagoDTO
    {
        $this->repository->findById($command->id);

        $changes = array_filter([
            'nombre' => $command->nombre,
            'orden'  => $command->orden,
        ], fn ($v) => $v !== null);

        if ($command->activo !== null) {
            $changes['activo'] = $command->activo;
        }
        $changes['updated_at'] = now()->toDateTimeString();

        $this->repository->update($command->id, $changes);

        return MedioPagoDTO::fromRow($this->repository->findById($command->id));
    }
}
