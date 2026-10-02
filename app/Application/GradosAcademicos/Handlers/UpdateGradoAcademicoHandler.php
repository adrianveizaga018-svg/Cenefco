<?php

namespace App\Application\GradosAcademicos\Handlers;

use App\Application\GradosAcademicos\Commands\UpdateGradoAcademicoCommand;
use App\Application\GradosAcademicos\DTOs\GradoAcademicoDTO;
use App\Domain\GradosAcademicos\Contracts\GradoAcademicoRepositoryInterface;

class UpdateGradoAcademicoHandler
{
    public function __construct(private readonly GradoAcademicoRepositoryInterface $repository) {}

    public function handle(UpdateGradoAcademicoCommand $command): GradoAcademicoDTO
    {
        $this->repository->findById($command->id);

        $changes = array_filter([
            'nombre'      => $command->nombre,
            'abreviatura' => $command->abreviatura,
            'orden'       => $command->orden,
        ], fn ($v) => $v !== null);

        if ($command->requiere_titulo !== null) {
            $changes['requiere_titulo'] = $command->requiere_titulo;
        }
        if ($command->activo !== null) {
            $changes['activo'] = $command->activo;
        }
        $changes['updated_at'] = now()->toDateTimeString();

        $this->repository->update($command->id, $changes);

        return GradoAcademicoDTO::fromRow($this->repository->findById($command->id));
    }
}
