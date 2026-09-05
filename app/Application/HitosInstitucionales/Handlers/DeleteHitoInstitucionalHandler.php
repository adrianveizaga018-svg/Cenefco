<?php

namespace App\Application\HitosInstitucionales\Handlers;

use App\Application\HitosInstitucionales\Commands\DeleteHitoInstitucionalCommand;
use App\Domain\HitosInstitucionales\Contracts\HitoInstitucionalRepositoryInterface;

class DeleteHitoInstitucionalHandler
{
    public function __construct(private readonly HitoInstitucionalRepositoryInterface $repository) {}

    public function handle(DeleteHitoInstitucionalCommand $c): void
    {
        $this->repository->delete($c->id);
    }
}
