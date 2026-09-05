<?php

namespace App\Application\TiposPrograma\Handlers;

use App\Application\TiposPrograma\Commands\DeleteTipoProgramaCommand;
use App\Domain\TiposPrograma\Contracts\TipoProgramaRepositoryInterface;

class DeleteTipoProgramaHandler
{
    public function __construct(private readonly TipoProgramaRepositoryInterface $repository) {}

    public function handle(DeleteTipoProgramaCommand $command): void
    {
        $this->repository->delete($command->idTipoprograma);
    }
}
