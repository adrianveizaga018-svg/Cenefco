<?php
namespace App\Application\GruposAcademicos\Handlers;
use App\Application\GruposAcademicos\Commands\DeleteGrupoAcademicoCommand;
use App\Domain\GruposAcademicos\Contracts\GrupoAcademicoRepositoryInterface;
class DeleteGrupoAcademicoHandler {
    public function __construct(private readonly GrupoAcademicoRepositoryInterface $repository) {}
    public function handle(DeleteGrupoAcademicoCommand $command): void {
        $this->repository->delete($command->idGrupo);
    }
}
