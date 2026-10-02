<?php

declare(strict_types=1);

namespace App\Domain\CalendarioAcademico\Contracts;

use App\Application\CalendarioAcademico\Commands\CreateCalendarioAcademicoCommand;
use App\Application\CalendarioAcademico\Commands\UpdateCalendarioAcademicoCommand;
use App\Application\CalendarioAcademico\DTOs\CalendarioAcademicoDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Pagination\LengthAwarePaginator;

interface CalendarioAcademicoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloPublicos = false, ?string $tipo = null): LengthAwarePaginator;

    public function paraReporte(string $query = '', ?string $tipo = null, bool $soloPublicos = false): array;

    public function cursosVigentes(): array;

    public function findById(int|string $id): CalendarioAcademicoDTO;

    public function create(CreateCalendarioAcademicoCommand $command): CalendarioAcademicoDTO;

    public function update(UpdateCalendarioAcademicoCommand $command): CalendarioAcademicoDTO;

    public function delete(int|string $id): void;
}
