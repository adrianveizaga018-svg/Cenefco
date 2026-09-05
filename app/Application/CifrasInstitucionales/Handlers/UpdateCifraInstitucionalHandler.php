<?php

namespace App\Application\CifrasInstitucionales\Handlers;

use App\Application\CifrasInstitucionales\Commands\UpdateCifraInstitucionalCommand;
use App\Application\CifrasInstitucionales\DTOs\CifraInstitucionalDTO;
use App\Domain\CifrasInstitucionales\Contracts\CifraInstitucionalRepositoryInterface;

class UpdateCifraInstitucionalHandler
{
    public function __construct(private readonly CifraInstitucionalRepositoryInterface $repository) {}

    public function handle(UpdateCifraInstitucionalCommand $c): CifraInstitucionalDTO
    {
        $data = array_filter([
            'valor'       => $c->valor,
            'etiqueta'    => $c->etiqueta,
            'descripcion' => $c->descripcion,
            'icono'       => $c->icono,
            'color'       => $c->color,
            'orden'       => $c->orden,
            'activo'      => $c->activo,
        ], fn ($v) => $v !== null);

        return $this->repository->update($c->id, $data);
    }
}
