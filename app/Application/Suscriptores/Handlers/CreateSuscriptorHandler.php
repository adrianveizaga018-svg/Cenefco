<?php

declare(strict_types=1);

namespace App\Application\Suscriptores\Handlers;

use App\Application\Suscriptores\Commands\CreateSuscriptorCommand;
use App\Application\Suscriptores\DTOs\SuscriptorDTO;
use App\Domain\Suscriptores\Contracts\SuscriptorRepositoryInterface;

class CreateSuscriptorHandler
{
    public function __construct(
        private readonly SuscriptorRepositoryInterface $repository,
    ) {}

    public function handle(CreateSuscriptorCommand $command): SuscriptorDTO
    {
        return $this->repository->create($command);
    }
}
