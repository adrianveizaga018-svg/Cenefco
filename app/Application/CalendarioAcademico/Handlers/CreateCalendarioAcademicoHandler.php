<?php

declare(strict_types=1);

namespace App\Application\CalendarioAcademico\Handlers;

use App\Application\CalendarioAcademico\Commands\CreateCalendarioAcademicoCommand;
use App\Application\CalendarioAcademico\DTOs\CalendarioAcademicoDTO;
use App\Domain\CalendarioAcademico\Contracts\CalendarioAcademicoRepositoryInterface;

class CreateCalendarioAcademicoHandler
{
    public function __construct(
        private readonly CalendarioAcademicoRepositoryInterface $repository,
    ) {}

    public function handle(CreateCalendarioAcademicoCommand $command): CalendarioAcademicoDTO
    {
        return $this->repository->create($command);
    }
}
