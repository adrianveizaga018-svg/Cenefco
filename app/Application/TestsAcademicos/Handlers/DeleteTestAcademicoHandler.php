<?php
namespace App\Application\TestsAcademicos\Handlers;
use App\Application\TestsAcademicos\Commands\DeleteTestAcademicoCommand;
use App\Domain\TestsAcademicos\Contracts\TestAcademicoRepositoryInterface;
class DeleteTestAcademicoHandler {
    public function __construct(private readonly TestAcademicoRepositoryInterface $repository) {}
    public function handle(DeleteTestAcademicoCommand $command): void {
        $this->repository->delete($command->idTest);
    }
}
