<?php

declare(strict_types=1);

namespace App\Application\CalendarioAcademico\Handlers;

use App\Application\CalendarioAcademico\Commands\UpdateCalendarioAcademicoCommand;
use App\Application\CalendarioAcademico\DTOs\CalendarioAcademicoDTO;
use App\Domain\CalendarioAcademico\Contracts\CalendarioAcademicoRepositoryInterface;

class UpdateCalendarioAcademicoHandler
{
    public function __construct(
        private readonly CalendarioAcademicoRepositoryInterface $repository,
    ) {}

    public function handle(UpdateCalendarioAcademicoCommand $command): CalendarioAcademicoDTO
    {
        return $this->repository->update($command);
    }
}
