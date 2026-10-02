<?php

namespace App\Application\Noticias\Handlers;

use App\Application\Noticias\Commands\UpdateNoticiaCommand;
use App\Application\Noticias\DTOs\NoticiaDTO;
use App\Domain\Noticias\Contracts\NoticiaRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UpdateNoticiaHandler
{
    public function __construct(
        private readonly NoticiaRepositoryInterface $repository
    ) {}

    public function handle(UpdateNoticiaCommand $command): NoticiaDTO
    {
        return DB::transaction(function () use ($command) {
            $dto = $this->repository->update($command->id, $command->data);

            if (isset($command->etiquetas)) {
                $this->repository->syncEtiquetas($command->id, $command->etiquetas);
            }

            return $this->repository->findById($command->id);
        });
    }
}
