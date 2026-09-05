<?php

namespace App\Application\HistoriaMunicipio\Handlers;

use App\Application\HistoriaMunicipio\Commands\DeleteHistoriaMunicipioCommand;
use App\Domain\HistoriaMunicipio\Contracts\HistoriaMunicipioRepositoryInterface;

class DeleteHistoriaMunicipioHandler
{
    public function __construct(private readonly HistoriaMunicipioRepositoryInterface $repository) {}

    public function handle(DeleteHistoriaMunicipioCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
