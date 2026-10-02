<?php

namespace App\Application\CifrasInstitucionales\Handlers;

use App\Application\CifrasInstitucionales\Commands\DeleteCifraInstitucionalCommand;
use App\Domain\CifrasInstitucionales\Contracts\CifraInstitucionalRepositoryInterface;

class DeleteCifraInstitucionalHandler
{
    public function __construct(private readonly CifraInstitucionalRepositoryInterface $repository) {}

    public function handle(DeleteCifraInstitucionalCommand $c): void
    {
        $this->repository->delete($c->id);
    }
}
