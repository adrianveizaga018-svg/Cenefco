<?php

namespace App\Application\CertPlantillas\Handlers;

use App\Application\CertPlantillas\Commands\DeleteCertPlantillaCommand;
use App\Domain\CertPlantillas\Contracts\CertPlantillaRepositoryInterface;

class DeleteCertPlantillaHandler
{
    public function __construct(
        private readonly CertPlantillaRepositoryInterface $repository
    ) {}

    public function handle(DeleteCertPlantillaCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
