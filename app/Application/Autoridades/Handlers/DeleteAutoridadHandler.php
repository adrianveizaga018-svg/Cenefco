<?php

namespace App\Application\Autoridades\Handlers;

use App\Application\Autoridades\Commands\DeleteAutoridadCommand;
use App\Domain\Autoridades\Contracts\AutoridadRepositoryInterface;

class DeleteAutoridadHandler
{
    public function __construct(private readonly AutoridadRepositoryInterface $repository) {}

    public function handle(DeleteAutoridadCommand $command): bool
    {
        return $this->repository->delete($command->id);
    }
}
