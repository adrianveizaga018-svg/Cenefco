<?php

namespace App\Application\CampanasPublicidad\Handlers;

use App\Application\CampanasPublicidad\Commands\DeleteCampanaPublicidadCommand;
use App\Domain\CampanasPublicidad\Contracts\CampanaPublicidadRepositoryInterface;
use App\Domain\CampanasPublicidad\Exceptions\CampanaPublicidadConGastosException;

class DeleteCampanaPublicidadHandler
{
    public function __construct(private readonly CampanaPublicidadRepositoryInterface $repository) {}

    public function handle(DeleteCampanaPublicidadCommand $c): bool
    {
        if ($this->repository->tieneGastos($c->id)) {
            throw new CampanaPublicidadConGastosException();
        }

        return $this->repository->delete($c->id);
    }
}
