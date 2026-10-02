<?php

namespace App\Application\Ajustes\Handlers;

use App\Application\Ajustes\Commands\UpdateAjusteCommand;
use App\Application\Ajustes\DTOs\AjusteDTO;
use App\Domain\Ajustes\Contracts\AjusteRepositoryInterface;

class UpdateAjusteHandler
{
    public function __construct(private readonly AjusteRepositoryInterface $repository) {}

    public function handle(UpdateAjusteCommand $command): AjusteDTO
    {
        return $this->repository->update($command->clave, ['valor' => $command->valor]);
    }
}
