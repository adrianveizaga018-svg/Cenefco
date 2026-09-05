<?php
namespace App\Application\GruposAcademicos\Handlers;
use App\Application\GruposAcademicos\Commands\CreateGrupoAcademicoCommand;
use App\Application\GruposAcademicos\DTOs\GrupoAcademicoDTO;
use App\Domain\GruposAcademicos\Contracts\GrupoAcademicoRepositoryInterface;
class CreateGrupoAcademicoHandler {
    public function __construct(private readonly GrupoAcademicoRepositoryInterface $repository) {}
    public function handle(CreateGrupoAcademicoCommand $command): GrupoAcademicoDTO {
        $row = $this->repository->create([
            'id_grupo'  => $command->idGrupo,
            'id_test'   => $command->idTest,
            'nombre'    => $command->nombre,
            'id_us_reg' => $command->idUsReg,
            'fecha_reg' => now(),
            'estado'    => 1,
        ]);
        return GrupoAcademicoDTO::fromRow($row);
    }
}
