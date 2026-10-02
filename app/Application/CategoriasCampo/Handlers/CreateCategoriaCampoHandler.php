<?php

namespace App\Application\CategoriasCampo\Handlers;

use App\Application\CategoriasCampo\Commands\CreateCategoriaCampoCommand;
use App\Application\CategoriasCampo\DTOs\CategoriaCampoDTO;
use App\Domain\CategoriasCampo\Contracts\CategoriaCampoRepositoryInterface;

class CreateCategoriaCampoHandler
{
    public function __construct(
        private readonly CategoriaCampoRepositoryInterface $repository,
    ) {}

    public function handle(CreateCategoriaCampoCommand $command): CategoriaCampoDTO
    {
        return $this->repository->create([
            'categoria_id' => $command->categoria_id,
            'nombre_campo' => $command->nombre_campo,
            'etiqueta'     => $command->etiqueta,
            'tipo_campo'   => $command->tipo_campo,
            'requerido'    => $command->requerido,
            'orden'        => $command->orden,
            'paso'         => $command->paso,
            'activo'       => $command->activo,
            'ayuda'        => $command->ayuda,
            'opciones'     => $command->opciones,
            'validacion'   => $command->validacion,
        ]);
    }
}
