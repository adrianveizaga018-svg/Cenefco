<?php

namespace App\Application\HitosInstitucionales\Handlers;

use App\Application\HitosInstitucionales\Commands\CreateHitoInstitucionalCommand;
use App\Application\HitosInstitucionales\DTOs\HitoInstitucionalDTO;
use App\Domain\HitosInstitucionales\Contracts\HitoInstitucionalRepositoryInterface;

class CreateHitoInstitucionalHandler
{
    public function __construct(private readonly HitoInstitucionalRepositoryInterface $repository) {}

    public function handle(CreateHitoInstitucionalCommand $c): HitoInstitucionalDTO
    {
        return $this->repository->create([
            'anio'        => $c->anio,
            'titulo'      => $c->titulo,
            'descripcion' => $c->descripcion,
            'imagen_url'  => $c->imagen_url,
            'imagen_alt'  => $c->imagen_alt,
            'orden'       => $c->orden,
            'activo'      => $c->activo,
        ]);
    }
}
