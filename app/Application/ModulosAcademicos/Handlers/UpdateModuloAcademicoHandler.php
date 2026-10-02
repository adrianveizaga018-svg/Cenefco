<?php
namespace App\Application\ModulosAcademicos\Handlers;
use App\Application\ModulosAcademicos\Commands\UpdateModuloAcademicoCommand;
use App\Domain\ModulosAcademicos\Contracts\ModuloAcademicoRepositoryInterface;
class UpdateModuloAcademicoHandler {
    public function __construct(private readonly ModuloAcademicoRepositoryInterface $repository) {}
    public function handle(UpdateModuloAcademicoCommand $command): void {
        $this->repository->update($command->idMod, array_filter([
            'nombre'      => $command->nombre,
            'descripcion' => $command->descripcion,
            'posicion'    => $command->posicion,
        ], fn ($v) => $v !== null));
    }
}
