<?php

namespace App\Application\CertPlantillaCampos\Handlers;

use App\Application\CertPlantillaCampos\Commands\DeleteCertPlantillaCampoCommand;
use App\Domain\CertPlantillaCampos\Contracts\CertPlantillaCampoRepositoryInterface;

class DeleteCertPlantillaCampoHandler
{
    public function __construct(
        private readonly CertPlantillaCampoRepositoryInterface $repository
    ) {}

    public function handle(DeleteCertPlantillaCampoCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
