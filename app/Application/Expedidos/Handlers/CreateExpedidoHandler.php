<?php

namespace App\Application\Expedidos\Handlers;

use App\Application\Expedidos\Commands\CreateExpedidoCommand;
use App\Application\Expedidos\DTOs\ExpedidoDTO;
use App\Domain\Expedidos\Contracts\ExpedidoRepositoryInterface;

class CreateExpedidoHandler
{
    public function __construct(private readonly ExpedidoRepositoryInterface $repository) {}

    public function handle(CreateExpedidoCommand $command): ExpedidoDTO
    {
        $row = $this->repository->create([
            'nombre'     => $command->nombre,
            'orden'      => $command->orden,
            'activo'     => $command->activo,
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ]);

        return ExpedidoDTO::fromRow($row);
    }
}
