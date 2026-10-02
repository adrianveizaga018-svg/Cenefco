<?php

namespace App\Application\Comunicados\Handlers;

use App\Application\Comunicados\Commands\CreateComunicadoCommand;
use App\Application\Comunicados\DTOs\ComunicadoDTO;
use App\Domain\Comunicados\Contracts\ComunicadoRepositoryInterface;

class CreateComunicadoHandler
{
    public function __construct(
        private readonly ComunicadoRepositoryInterface $repository
    ) {}

    public function handle(CreateComunicadoCommand $command): ComunicadoDTO
    {
        $model = $this->repository->create([
            'titulo' => $command->titulo,
            'resumen' => $command->resumen,
            'cuerpo' => $command->cuerpo,
            'imagen_url' => $command->imagen_url,
            'archivo_url' => $command->archivo_url,
            'estado' => $command->estado,
            'destacado' => $command->destacado,
            'fecha_publicacion' => $command->fecha_publicacion,
        ]);

        return ComunicadoDTO::fromModel($model);
    }
}
