<?php
namespace App\Application\FormulariosAcademicos\Handlers;
use App\Application\FormulariosAcademicos\Commands\CreateFormularioAcademicoCommand;
use App\Application\FormulariosAcademicos\DTOs\FormularioAcademicoDTO;
use App\Domain\FormulariosAcademicos\Contracts\FormularioAcademicoRepositoryInterface;
class CreateFormularioAcademicoHandler {
    public function __construct(private readonly FormularioAcademicoRepositoryInterface $repository) {}
    public function handle(CreateFormularioAcademicoCommand $command): FormularioAcademicoDTO {
        $row = $this->repository->create([
            'id_formulario' => $command->idFormulario,
            'nombre'        => $command->nombre,
            'descripcion'   => $command->descripcion,
            'id_us_reg'     => $command->idUsReg,
            'fecha_reg'     => now(),
            'estado'        => 1,
        ]);
        return FormularioAcademicoDTO::fromRow($row);
    }
}
