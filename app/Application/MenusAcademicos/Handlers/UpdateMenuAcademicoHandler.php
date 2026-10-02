<?php
namespace App\Application\MenusAcademicos\Handlers;
use App\Application\MenusAcademicos\Commands\UpdateMenuAcademicoCommand;
use App\Domain\MenusAcademicos\Contracts\MenuAcademicoRepositoryInterface;
class UpdateMenuAcademicoHandler {
    public function __construct(private readonly MenuAcademicoRepositoryInterface $repository) {}
    public function handle(UpdateMenuAcademicoCommand $command): void {
        $this->repository->update($command->idMen, array_filter([
            'id_mod'      => $command->idMod,
            'nombre'      => $command->nombre,
            'descripcion' => $command->descripcion,
        ], fn ($v) => $v !== null));
    }
}
