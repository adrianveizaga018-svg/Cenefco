<?php

namespace App\Application\GradosAcademicos\Handlers;

use App\Application\GradosAcademicos\Commands\CreateGradoAcademicoCommand;
use App\Application\GradosAcademicos\DTOs\GradoAcademicoDTO;
use App\Domain\GradosAcademicos\Contracts\GradoAcademicoRepositoryInterface;

class CreateGradoAcademicoHandler
{
    public function __construct(private readonly GradoAcademicoRepositoryInterface $repository) {}

    public function handle(CreateGradoAcademicoCommand $command): GradoAcademicoDTO
    {
        $row = $this->repository->create([
            'nombre'          => $command->nombre,
            'abreviatura'     => $command->abreviatura,
            'requiere_titulo' => $command->requiere_titulo,
            'orden'           => $command->orden,
            'activo'          => $command->activo,
            'created_at'      => now()->toDateTimeString(),
            'updated_at'      => now()->toDateTimeString(),
        ]);

        return GradoAcademicoDTO::fromRow($row);
    }
}
