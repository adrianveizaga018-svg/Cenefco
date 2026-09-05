<?php

namespace App\Application\Formularios\QueryHandlers;

use App\Domain\Formularios\Contracts\FormularioRepositoryInterface;

class GetFormulariosActivosQueryHandler
{
    public function __construct(private readonly FormularioRepositoryInterface $repository) {}

    public function handle(): array
    {
        return $this->repository->findAllActivos();
    }
}
