<?php

declare(strict_types=1);

namespace App\Application\Suscriptores\Handlers;

use App\Application\Suscriptores\Commands\DeleteSuscriptorCommand;
use App\Domain\Suscriptores\Contracts\SuscriptorRepositoryInterface;

class DeleteSuscriptorHandler
{
    public function __construct(
        private readonly SuscriptorRepositoryInterface $repository,
    ) {}

    public function handle(DeleteSuscriptorCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
