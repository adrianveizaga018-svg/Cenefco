<?php

namespace App\Application\CategoriasCampo\Handlers;

use App\Application\CategoriasCampo\Commands\UpdateCategoriaCampoCommand;
use App\Application\CategoriasCampo\DTOs\CategoriaCampoDTO;
use App\Domain\CategoriasCampo\Contracts\CategoriaCampoRepositoryInterface;

class UpdateCategoriaCampoHandler
{
    public function __construct(
        private readonly CategoriaCampoRepositoryInterface $repository,
    ) {}

    public function handle(UpdateCategoriaCampoCommand $command): CategoriaCampoDTO
    {
        $data = array_filter([
            'etiqueta'   => $command->etiqueta,
            'tipo_campo' => $command->tipo_campo,
            'requerido'  => $command->requerido,
            'orden'      => $command->orden,
            'paso'       => $command->paso,
            'activo'     => $command->activo,
            'ayuda'      => $command->ayuda,
            'opciones'   => $command->opciones,
            'validacion' => $command->validacion,
        ], fn ($v) => $v !== null);

        return $this->repository->update($command->id, $data);
    }
}
