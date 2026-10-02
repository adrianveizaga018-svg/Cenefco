<?php

namespace App\Application\Imparticiones\Handlers;

use App\Application\Imparticiones\Commands\DeleteImparteCommand;
use App\Domain\Imparticiones\Contracts\ImparteRepositoryInterface;
use App\Domain\Imparticiones\Exceptions\ImparteConInscritosException;

class DeleteImparteHandler
{
    public function __construct(
        private readonly ImparteRepositoryInterface $repository
    ) {}

    public function handle(DeleteImparteCommand $command): void
    {
        if ($this->repository->tieneInscritos($command->id)) {
            throw new ImparteConInscritosException();
        }

        $this->repository->delete($command->id);
    }
}
