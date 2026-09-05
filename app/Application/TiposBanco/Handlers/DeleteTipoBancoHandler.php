<?php

namespace App\Application\TiposBanco\Handlers;

use App\Application\TiposBanco\Commands\DeleteTipoBancoCommand;
use App\Domain\TiposBanco\Contracts\TipoBancoRepositoryInterface;

class DeleteTipoBancoHandler
{
    public function __construct(private readonly TipoBancoRepositoryInterface $repository) {}

    public function handle(DeleteTipoBancoCommand $c): bool
    {
        return $this->repository->delete($c->id);
    }
}
