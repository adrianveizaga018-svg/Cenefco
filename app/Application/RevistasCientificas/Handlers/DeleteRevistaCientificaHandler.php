<?php

namespace App\Application\RevistasCientificas\Handlers;

use App\Application\RevistasCientificas\Commands\DeleteRevistaCientificaCommand;
use App\Domain\RevistasCientificas\Contracts\RevistaCientificaRepositoryInterface;

class DeleteRevistaCientificaHandler
{
    public function __construct(private readonly RevistaCientificaRepositoryInterface $repository) {}

    public function handle(DeleteRevistaCientificaCommand $command): void
    {
        $this->repository->findById($command->id);
        $this->repository->softDelete($command->id);
    }
}
