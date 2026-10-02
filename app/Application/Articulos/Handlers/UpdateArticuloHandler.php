<?php

namespace App\Application\Articulos\Handlers;

use App\Application\Articulos\Commands\UpdateArticuloCommand;
use App\Application\Articulos\DTOs\ArticuloDTO;
use App\Domain\Articulos\Contracts\ArticuloRepositoryInterface;

class UpdateArticuloHandler
{
    public function __construct(private readonly ArticuloRepositoryInterface $repository) {}

    public function handle(UpdateArticuloCommand $command): ArticuloDTO
    {
        $changes = array_filter([
            'titulo'               => $command->titulo,
            'entradilla'           => $command->entradilla,
            'contenido'            => $command->contenido,
            'imagen_principal_url' => $command->imagen_principal_url,
            'imagen_alt'           => $command->imagen_alt,
            'fecha_publicacion'    => $command->fecha_publicacion,
            'estado_web'           => $command->estado_web,
            'meta_titulo'          => $command->meta_titulo,
            'meta_descripcion'     => $command->meta_descripcion,
            'estado'               => $command->estado,
        ], fn ($v) => $v !== null);

        if ($command->destacada !== null) {
            $changes['destacada'] = $command->destacada;
        }

        if ($command->slug !== null) {
            $changes['slug'] = $this->repository->uniqueSlug($command->slug, $command->id);
        }

        $changes['updated_at'] = now()->toDateTimeString();

        $row = $this->repository->update($command->id, $changes, $command->etiquetas);

        return ArticuloDTO::fromRow($row);
    }
}
