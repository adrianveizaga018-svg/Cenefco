<?php
namespace App\Application\TestsAcademicos\Handlers;
use App\Application\TestsAcademicos\Commands\CreateTestAcademicoCommand;
use App\Application\TestsAcademicos\DTOs\TestAcademicoDTO;
use App\Domain\TestsAcademicos\Contracts\TestAcademicoRepositoryInterface;
class CreateTestAcademicoHandler {
    public function __construct(private readonly TestAcademicoRepositoryInterface $repository) {}
    public function handle(CreateTestAcademicoCommand $command): TestAcademicoDTO {
        $row = $this->repository->create([
            'id_test'       => $command->idTest,
            'nombre'        => $command->nombre,
            'habilitar_test'=> $command->habilitarTest,
            'id_us_reg'     => $command->idUsReg,
            'fecha_reg'     => now(),
            'estado'        => 1,
        ]);
        return TestAcademicoDTO::fromRow($row);
    }
}
