<?php

namespace App\Application\Formularios\Handlers;

use App\Application\Formularios\Commands\UpdateFormularioCommand;
use App\Application\Formularios\DTOs\FormularioDTO;
use App\Domain\Formularios\Contracts\FormularioRepositoryInterface;

class UpdateFormularioHandler
{
    public function __construct(private readonly FormularioRepositoryInterface $repository) {}

    public function handle(UpdateFormularioCommand $c): FormularioDTO
    {
        $data = array_filter([
            'nombre'      => $c->nombre,
            'slug'        => $c->slug,
            'descripcion' => $c->descripcion,
        ], fn ($v) => $v !== null);

        if ($c->campos !== null) {
            $data['campos'] = $c->campos;
        }
        if ($c->activo !== null) {
            $data['activo'] = $c->activo;
        }

        return $this->repository->update($c->id, $data);
    }
}
