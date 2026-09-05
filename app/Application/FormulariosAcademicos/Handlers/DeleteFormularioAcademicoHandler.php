<?php
namespace App\Application\FormulariosAcademicos\Handlers;
use App\Application\FormulariosAcademicos\Commands\DeleteFormularioAcademicoCommand;
use App\Domain\FormulariosAcademicos\Contracts\FormularioAcademicoRepositoryInterface;
class DeleteFormularioAcademicoHandler {
    public function __construct(private readonly FormularioAcademicoRepositoryInterface $repository) {}
    public function handle(DeleteFormularioAcademicoCommand $command): void {
        $this->repository->delete($command->idFormulario);
    }
}
