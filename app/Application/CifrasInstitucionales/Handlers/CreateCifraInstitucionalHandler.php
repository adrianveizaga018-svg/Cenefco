<?php

namespace App\Application\CifrasInstitucionales\Handlers;

use App\Application\CifrasInstitucionales\Commands\CreateCifraInstitucionalCommand;
use App\Application\CifrasInstitucionales\DTOs\CifraInstitucionalDTO;
use App\Domain\CifrasInstitucionales\Contracts\CifraInstitucionalRepositoryInterface;

class CreateCifraInstitucionalHandler
{
    public function __construct(private readonly CifraInstitucionalRepositoryInterface $repository) {}

    public function handle(CreateCifraInstitucionalCommand $c): CifraInstitucionalDTO
    {
        return $this->repository->create([
            'valor'       => $c->valor,
            'etiqueta'    => $c->etiqueta,
            'descripcion' => $c->descripcion,
            'icono'       => $c->icono,
            'color'       => $c->color,
            'orden'       => $c->orden,
            'activo'      => $c->activo,
        ]);
    }
}
