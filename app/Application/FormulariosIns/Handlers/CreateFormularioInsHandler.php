<?php
namespace App\Application\FormulariosIns\Handlers;
use App\Application\FormulariosIns\Commands\CreateFormularioInsCommand;
use App\Application\FormulariosIns\DTOs\FormularioInsDTO;
use App\Domain\FormulariosIns\Contracts\FormularioInsRepositoryInterface;
class CreateFormularioInsHandler {
    public function __construct(private readonly FormularioInsRepositoryInterface $repository) {}
    public function handle(CreateFormularioInsCommand $command): FormularioInsDTO {
        $row = $this->repository->create([
            'id_formins'  => $command->idFormins,
            'id_imp'      => $command->idImp,
            'nombre'      => $command->nombre,
            'descripcion' => $command->descripcion,
            'id_us_reg'   => $command->idUsReg,
            'fecha_reg'   => now(),
            'estado'      => 1,
        ]);
        return FormularioInsDTO::fromRow($row);
    }
}
