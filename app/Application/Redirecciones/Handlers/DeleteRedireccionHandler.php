<?php

namespace App\Application\Redirecciones\Handlers;

use App\Application\Redirecciones\Commands\DeleteRedireccionCommand;
use App\Domain\Redirecciones\Contracts\RedireccionRepositoryInterface;

class DeleteRedireccionHandler
{
    public function __construct(private readonly RedireccionRepositoryInterface $repository) {}

    public function handle(DeleteRedireccionCommand $c): bool
    {
        return $this->repository->delete($c->id);
    }
}
