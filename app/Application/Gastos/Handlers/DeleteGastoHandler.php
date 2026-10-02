<?php

namespace App\Application\Gastos\Handlers;

use App\Application\Gastos\Commands\DeleteGastoCommand;
use App\Domain\Gastos\Contracts\GastoRepositoryInterface;

class DeleteGastoHandler
{
    public function __construct(private readonly GastoRepositoryInterface $repository) {}

    public function handle(DeleteGastoCommand $c): bool
    {
        return $this->repository->delete($c->id);
    }
}
