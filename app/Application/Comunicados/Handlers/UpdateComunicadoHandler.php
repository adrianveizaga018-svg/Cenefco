<?php

namespace App\Application\Comunicados\Handlers;

use App\Application\Comunicados\Commands\UpdateComunicadoCommand;
use App\Application\Comunicados\DTOs\ComunicadoDTO;
use App\Domain\Comunicados\Contracts\ComunicadoRepositoryInterface;

class UpdateComunicadoHandler
{
    public function __construct(
        private readonly ComunicadoRepositoryInterface $repository
    ) {}

    public function handle(UpdateComunicadoCommand $command): ComunicadoDTO
    {
        $model = $this->repository->update($command->id, [
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
