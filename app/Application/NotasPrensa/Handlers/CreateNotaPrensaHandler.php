<?php

declare(strict_types=1);

namespace App\Application\NotasPrensa\Handlers;

use App\Application\NotasPrensa\Commands\CreateNotaPrensaCommand;
use App\Application\NotasPrensa\DTOs\NotaPrensaDTO;
use App\Domain\NotasPrensa\Contracts\NotaPrensaRepositoryInterface;

class CreateNotaPrensaHandler
{
    public function __construct(
        private readonly NotaPrensaRepositoryInterface $repository,
    ) {}

    public function handle(CreateNotaPrensaCommand $command): NotaPrensaDTO
    {
        return $this->repository->create($command);
    }
}
