<?php

namespace App\Application\HistoriaMunicipio\Handlers;

use App\Application\HistoriaMunicipio\Commands\UpdateHistoriaMunicipioCommand;
use App\Application\HistoriaMunicipio\DTOs\HistoriaMunicipioDTO;
use App\Domain\HistoriaMunicipio\Contracts\HistoriaMunicipioRepositoryInterface;

class UpdateHistoriaMunicipioHandler
{
    public function __construct(private readonly HistoriaMunicipioRepositoryInterface $repository) {}

    public function handle(UpdateHistoriaMunicipioCommand $command): HistoriaMunicipioDTO
    {
        return $this->repository->update($command->id, $command->data);
    }
}
