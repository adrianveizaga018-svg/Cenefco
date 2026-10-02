<?php

namespace App\Application\Noticias\Handlers;

use App\Application\Noticias\Commands\DeleteNoticiaCommand;
use App\Domain\Noticias\Contracts\NoticiaRepositoryInterface;

class DeleteNoticiaHandler
{
    public function __construct(
        private readonly NoticiaRepositoryInterface $repository
    ) {}

    public function handle(DeleteNoticiaCommand $command): bool
    {
        return $this->repository->delete($command->id);
    }
}
