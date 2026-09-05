<?php
namespace App\Application\ModulosAcademicos\Handlers;
use App\Application\ModulosAcademicos\Commands\CreateModuloAcademicoCommand;
use App\Application\ModulosAcademicos\DTOs\ModuloAcademicoDTO;
use App\Domain\ModulosAcademicos\Contracts\ModuloAcademicoRepositoryInterface;
class CreateModuloAcademicoHandler {
    public function __construct(private readonly ModuloAcademicoRepositoryInterface $repository) {}
    public function handle(CreateModuloAcademicoCommand $command): ModuloAcademicoDTO {
        $row = $this->repository->create([
            'id_mod'      => $command->idMod,
            'nombre'      => $command->nombre,
            'descripcion' => $command->descripcion,
            'posicion'    => $command->posicion,
            'id_us_reg'   => $command->idUsReg,
            'fecha_reg'   => now(),
            'estado'      => 1,
        ]);
        return ModuloAcademicoDTO::fromRow($row);
    }
}
