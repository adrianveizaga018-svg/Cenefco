<?php

namespace App\Application\Formularios\Handlers;

use App\Application\Formularios\Commands\CreateFormularioCommand;
use App\Application\Formularios\DTOs\FormularioDTO;
use App\Domain\Formularios\Contracts\FormularioRepositoryInterface;
use Illuminate\Support\Str;

class CreateFormularioHandler
{
    public function __construct(private readonly FormularioRepositoryInterface $repository) {}

    public function handle(CreateFormularioCommand $c): FormularioDTO
    {
        return $this->repository->create([
            'nombre'      => $c->nombre,
            'slug'        => $c->slug ?: Str::slug($c->nombre),
            'descripcion' => $c->descripcion,
            'campos'      => $c->campos,
            'activo'      => $c->activo,
        ]);
    }
}
