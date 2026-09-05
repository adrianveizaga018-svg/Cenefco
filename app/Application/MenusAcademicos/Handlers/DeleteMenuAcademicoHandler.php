<?php
namespace App\Application\MenusAcademicos\Handlers;
use App\Application\MenusAcademicos\Commands\DeleteMenuAcademicoCommand;
use App\Domain\MenusAcademicos\Contracts\MenuAcademicoRepositoryInterface;
class DeleteMenuAcademicoHandler {
    public function __construct(private readonly MenuAcademicoRepositoryInterface $repository) {}
    public function handle(DeleteMenuAcademicoCommand $command): void {
        $this->repository->delete($command->idMen);
    }
}
