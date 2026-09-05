<?php

namespace App\Application\Honorarios\Handlers;

use App\Application\Honorarios\Commands\DeleteConfigHonorarioCommand;
use App\Domain\Honorarios\Contracts\ConfigHonorarioRepositoryInterface;

class DeleteConfigHonorarioHandler
{
    public function __construct(private readonly ConfigHonorarioRepositoryInterface $repository) {}

    public function handle(DeleteConfigHonorarioCommand $c): bool
    {
        return $this->repository->delete($c->id_programa);
    }
}
