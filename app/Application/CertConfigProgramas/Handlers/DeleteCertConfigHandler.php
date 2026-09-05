<?php

namespace App\Application\CertConfigProgramas\Handlers;

use App\Application\CertConfigProgramas\Commands\DeleteCertConfigCommand;
use App\Domain\CertConfigProgramas\Contracts\CertConfigProgramaRepositoryInterface;
use App\Domain\CertConfigProgramas\Exceptions\CertConfigNotFoundException;

class DeleteCertConfigHandler
{
    public function __construct(
        private readonly CertConfigProgramaRepositoryInterface $repo,
    ) {}

    public function handle(DeleteCertConfigCommand $command): void
    {
        $config = $this->repo->findById($command->id);
        if (! $config) {
            throw new CertConfigNotFoundException($command->id);
        }

        $this->repo->delete($command->id);
    }
}
