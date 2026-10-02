<?php
namespace App\Application\FormulariosAcademicos\Handlers;
use App\Application\FormulariosAcademicos\Commands\UpdateFormularioAcademicoCommand;
use App\Domain\FormulariosAcademicos\Contracts\FormularioAcademicoRepositoryInterface;
class UpdateFormularioAcademicoHandler {
    public function __construct(private readonly FormularioAcademicoRepositoryInterface $repository) {}
    public function handle(UpdateFormularioAcademicoCommand $command): void {
        $this->repository->update($command->idFormulario, array_filter([
            'nombre'      => $command->nombre,
            'descripcion' => $command->descripcion,
        ], fn ($v) => $v !== null));
    }
}
