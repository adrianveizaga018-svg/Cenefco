<?php

namespace App\Application\Articulos\Handlers;

use App\Application\Articulos\Commands\CreateArticuloCommand;
use App\Application\Articulos\DTOs\ArticuloDTO;
use App\Domain\Articulos\Contracts\ArticuloRepositoryInterface;
use Illuminate\Support\Str;

class CreateArticuloHandler
{
    public function __construct(private readonly ArticuloRepositoryInterface $repository) {}

    public function handle(CreateArticuloCommand $command): ArticuloDTO
    {
        $base = empty($command->slug)
            ? Str::slug($command->titulo)
            : $command->slug;

        $slug = $this->repository->uniqueSlug($base);

        $row = $this->repository->create([
            'slug'                 => $slug,
            'titulo'               => $command->titulo,
            'entradilla'           => $command->entradilla,
            'contenido'            => $command->contenido,
            'imagen_principal_url' => $command->imagen_principal_url,
            'imagen_alt'           => $command->imagen_alt,
            'destacada'            => $command->destacada,
            'fecha_publicacion'    => $command->fecha_publicacion,
            'estado_web'           => $command->estado_web,
            'meta_titulo'          => $command->meta_titulo,
            'meta_descripcion'     => $command->meta_descripcion,
            'estado'               => $command->estado,
            'id_us_reg'            => 0,
            'num_art'              => 0,
            'fecha_reg'            => now()->toDateTimeString(),
            'updated_at'           => now()->toDateTimeString(),
        ], $command->etiquetas);

        return ArticuloDTO::fromRow($row);
    }
}
