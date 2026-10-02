<?php

namespace App\Application\UsuariosPlanDoc\Handlers;

use App\Application\UsuariosPlanDoc\Commands\UpdateUsuarioPlanDocCommand;
use App\Application\UsuariosPlanDoc\DTOs\UsuarioPlanDocDTO;
use App\Domain\UsuariosPlanDoc\Contracts\UsuarioPlanDocRepositoryInterface;

class UpdateUsuarioPlanDocHandler
{
    public function __construct(
        private readonly UsuarioPlanDocRepositoryInterface $repository,
    ) {}

    public function handle(UpdateUsuarioPlanDocCommand $command): UsuarioPlanDocDTO
    {
        $data = array_filter([
            'id_plandoc' => $command->id_plandoc,
            'estado'     => $command->estado,
        ], fn ($v) => $v !== null);

        $row = $this->repository->update($command->id, $data);

        return UsuarioPlanDocDTO::fromRow($row);
    }
}
