<?php

declare(strict_types=1);

namespace App\Application\NotasPrensa\Handlers;

use App\Application\NotasPrensa\Commands\DeleteNotaPrensaCommand;
use App\Domain\NotasPrensa\Contracts\NotaPrensaRepositoryInterface;

class DeleteNotaPrensaHandler
{
    public function __construct(
        private readonly NotaPrensaRepositoryInterface $repository,
    ) {}

    public function handle(DeleteNotaPrensaCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
