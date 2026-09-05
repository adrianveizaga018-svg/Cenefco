<?php

namespace App\Application\CertConfigProgramas\Handlers;

use App\Application\CertConfigProgramas\Commands\DeleteCertConfigItemCommand;
use App\Domain\CertConfigProgramas\Contracts\CertConfigItemRepositoryInterface;
use App\Domain\CertConfigProgramas\Exceptions\CertConfigNotFoundException;

class DeleteCertConfigItemHandler
{
    public function __construct(
        private readonly CertConfigItemRepositoryInterface $repo,
    ) {}

    public function handle(DeleteCertConfigItemCommand $command): void
    {
        $item = $this->repo->findById($command->id);
        if (! $item) {
            throw new CertConfigNotFoundException($command->id);
        }

        $this->repo->delete($command->id);
    }
}
