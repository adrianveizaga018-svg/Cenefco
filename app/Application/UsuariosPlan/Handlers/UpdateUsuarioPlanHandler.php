<?php

namespace App\Application\UsuariosPlan\Handlers;

use App\Application\UsuariosPlan\Commands\UpdateUsuarioPlanCommand;
use App\Application\UsuariosPlan\DTOs\UsuarioPlanDTO;
use App\Domain\UsuariosPlan\Contracts\UsuarioPlanRepositoryInterface;

class UpdateUsuarioPlanHandler
{
    public function __construct(
        private readonly UsuarioPlanRepositoryInterface $repository,
    ) {}

    public function handle(UpdateUsuarioPlanCommand $command): UsuarioPlanDTO
    {
        $data = array_filter([
            'id_plan' => $command->id_plan,
            'estado'  => $command->estado,
        ], fn ($v) => $v !== null);

        $row = $this->repository->update($command->id, $data);

        return UsuarioPlanDTO::fromRow($row);
    }
}
