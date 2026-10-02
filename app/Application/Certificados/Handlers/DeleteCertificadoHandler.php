<?php

namespace App\Application\Certificados\Handlers;

use App\Application\Certificados\Commands\DeleteCertificadoCommand;
use App\Domain\Certificados\Contracts\CertificadoRepositoryInterface;

class DeleteCertificadoHandler
{
    public function __construct(
        private readonly CertificadoRepositoryInterface $repository
    ) {}

    public function handle(DeleteCertificadoCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
