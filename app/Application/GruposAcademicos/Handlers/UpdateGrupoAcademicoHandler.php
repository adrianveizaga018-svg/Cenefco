<?php
namespace App\Application\GruposAcademicos\Handlers;
use App\Application\GruposAcademicos\Commands\UpdateGrupoAcademicoCommand;
use App\Domain\GruposAcademicos\Contracts\GrupoAcademicoRepositoryInterface;
class UpdateGrupoAcademicoHandler {
    public function __construct(private readonly GrupoAcademicoRepositoryInterface $repository) {}
    public function handle(UpdateGrupoAcademicoCommand $command): void {
        $this->repository->update($command->idGrupo, array_filter([
            'id_test' => $command->idTest,
            'nombre'  => $command->nombre,
        ], fn ($v) => $v !== null));
    }
}
