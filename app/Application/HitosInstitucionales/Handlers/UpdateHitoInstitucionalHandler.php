<?php

namespace App\Application\HitosInstitucionales\Handlers;

use App\Application\HitosInstitucionales\Commands\UpdateHitoInstitucionalCommand;
use App\Application\HitosInstitucionales\DTOs\HitoInstitucionalDTO;
use App\Domain\HitosInstitucionales\Contracts\HitoInstitucionalRepositoryInterface;

class UpdateHitoInstitucionalHandler
{
    public function __construct(private readonly HitoInstitucionalRepositoryInterface $repository) {}

    public function handle(UpdateHitoInstitucionalCommand $c): HitoInstitucionalDTO
    {
        $data = array_filter([
            'anio'        => $c->anio,
            'titulo'      => $c->titulo,
            'descripcion' => $c->descripcion,
            'imagen_url'  => $c->imagen_url,
            'imagen_alt'  => $c->imagen_alt,
            'orden'       => $c->orden,
            'activo'      => $c->activo,
        ], fn ($v) => $v !== null);

        return $this->repository->update($c->id, $data);
    }
}
