<?php
namespace App\Application\MenusAcademicos\Handlers;
use App\Application\MenusAcademicos\Commands\CreateMenuAcademicoCommand;
use App\Application\MenusAcademicos\DTOs\MenuAcademicoDTO;
use App\Domain\MenusAcademicos\Contracts\MenuAcademicoRepositoryInterface;
class CreateMenuAcademicoHandler {
    public function __construct(private readonly MenuAcademicoRepositoryInterface $repository) {}
    public function handle(CreateMenuAcademicoCommand $command): MenuAcademicoDTO {
        $row = $this->repository->create([
            'id_men'      => $command->idMen,
            'id_mod'      => $command->idMod,
            'nombre'      => $command->nombre,
            'descripcion' => $command->descripcion,
            'id_us_reg'   => $command->idUsReg,
            'fecha_reg'   => now(),
            'estado'      => 1,
        ]);
        return MenuAcademicoDTO::fromRow($row);
    }
}
