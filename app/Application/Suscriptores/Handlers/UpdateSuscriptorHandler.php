<?php

declare(strict_types=1);

namespace App\Application\Suscriptores\Handlers;

use App\Application\Suscriptores\Commands\UpdateSuscriptorCommand;
use App\Application\Suscriptores\DTOs\SuscriptorDTO;
use App\Domain\Suscriptores\Contracts\SuscriptorRepositoryInterface;

class UpdateSuscriptorHandler
{
    public function __construct(
        private readonly SuscriptorRepositoryInterface $repository,
    ) {}

    public function handle(UpdateSuscriptorCommand $command): SuscriptorDTO
    {
        return $this->repository->update($command);
    }
}
