<?php
namespace App\Application\ModulosAcademicos\Handlers;
use App\Application\ModulosAcademicos\Commands\DeleteModuloAcademicoCommand;
use App\Domain\ModulosAcademicos\Contracts\ModuloAcademicoRepositoryInterface;
class DeleteModuloAcademicoHandler {
    public function __construct(private readonly ModuloAcademicoRepositoryInterface $repository) {}
    public function handle(DeleteModuloAcademicoCommand $command): void {
        $this->repository->delete($command->idMod);
    }
}
