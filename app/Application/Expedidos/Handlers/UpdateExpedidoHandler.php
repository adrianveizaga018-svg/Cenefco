<?php

namespace App\Application\Expedidos\Handlers;

use App\Application\Expedidos\Commands\UpdateExpedidoCommand;
use App\Application\Expedidos\DTOs\ExpedidoDTO;
use App\Domain\Expedidos\Contracts\ExpedidoRepositoryInterface;

class UpdateExpedidoHandler
{
    public function __construct(private readonly ExpedidoRepositoryInterface $repository) {}

    public function handle(UpdateExpedidoCommand $command): ExpedidoDTO
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

        return ExpedidoDTO::fromRow($this->repository->findById($command->id));
    }
}
