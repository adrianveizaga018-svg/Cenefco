<?php

declare(strict_types=1);

namespace App\Application\CalendarioAcademico\Handlers;

use App\Application\CalendarioAcademico\Commands\DeleteCalendarioAcademicoCommand;
use App\Domain\CalendarioAcademico\Contracts\CalendarioAcademicoRepositoryInterface;

class DeleteCalendarioAcademicoHandler
{
    public function __construct(
        private readonly CalendarioAcademicoRepositoryInterface $repository,
    ) {}

    public function handle(DeleteCalendarioAcademicoCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
