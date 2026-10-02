<?php
namespace App\Application\TestsAcademicos\Handlers;
use App\Application\TestsAcademicos\Commands\UpdateTestAcademicoCommand;
use App\Domain\TestsAcademicos\Contracts\TestAcademicoRepositoryInterface;
class UpdateTestAcademicoHandler {
    public function __construct(private readonly TestAcademicoRepositoryInterface $repository) {}
    public function handle(UpdateTestAcademicoCommand $command): void {
        $this->repository->update($command->idTest, array_filter([
            'nombre'         => $command->nombre,
            'habilitar_test' => $command->habilitarTest,
        ], fn ($v) => $v !== null));
    }
}
