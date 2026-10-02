<?php

namespace App\Application\MediosPago\Handlers;

use App\Application\MediosPago\Commands\CreateMedioPagoCommand;
use App\Application\MediosPago\DTOs\MedioPagoDTO;
use App\Domain\MediosPago\Contracts\MedioPagoRepositoryInterface;

class CreateMedioPagoHandler
{
    public function __construct(private readonly MedioPagoRepositoryInterface $repository) {}

    public function handle(CreateMedioPagoCommand $command): MedioPagoDTO
    {
        $row = $this->repository->create([
            'nombre'     => $command->nombre,
            'orden'      => $command->orden,
            'activo'     => $command->activo,
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ]);

        return MedioPagoDTO::fromRow($row);
    }
}
