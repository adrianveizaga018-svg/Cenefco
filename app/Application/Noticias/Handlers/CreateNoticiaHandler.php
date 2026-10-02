<?php

namespace App\Application\Noticias\Handlers;

use App\Application\Noticias\Commands\CreateNoticiaCommand;
use App\Application\Noticias\DTOs\NoticiaDTO;
use App\Domain\Noticias\Contracts\NoticiaRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CreateNoticiaHandler
{
    public function __construct(
        private readonly NoticiaRepositoryInterface $repository
    ) {}

    public function handle(CreateNoticiaCommand $command): NoticiaDTO
    {
        return DB::transaction(function () use ($command) {
            $model = $this->repository->create([
                'categoria_id' => $command->categoria_id,
                'autor_id' => $command->autor_id,
                'titulo' => $command->titulo,
                'entradilla' => $command->entradilla,
                'cuerpo' => $command->cuerpo,
                'imagen_principal_url' => $command->imagen_principal_url,
                'imagen_alt' => $command->imagen_alt,
                'estado' => $command->estado,
                'destacada' => $command->destacada,
                'fecha_publicacion' => $command->fecha_publicacion,
                'meta_titulo' => $command->meta_titulo,
                'meta_descripcion' => $command->meta_descripcion,
            ]);

            if (! empty($command->etiquetas)) {
                $this->repository->syncEtiquetas($model->id, $command->etiquetas);
            }

            return $this->repository->findById($model->id);
        });
    }
}
