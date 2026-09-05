<?php

declare(strict_types=1);

namespace App\Application\NotasPrensa\Handlers;

use App\Application\NotasPrensa\Commands\UpdateNotaPrensaCommand;
use App\Application\NotasPrensa\DTOs\NotaPrensaDTO;
use App\Domain\NotasPrensa\Contracts\NotaPrensaRepositoryInterface;

class UpdateNotaPrensaHandler
{
    public function __construct(
        private readonly NotaPrensaRepositoryInterface $repository,
    ) {}

    public function handle(UpdateNotaPrensaCommand $command): NotaPrensaDTO
    {
        return $this->repository->update($command);
    }
}
