<?php
namespace App\Application\FormulariosIns\Handlers;
use App\Application\FormulariosIns\Commands\UpdateFormularioInsCommand;
use App\Domain\FormulariosIns\Contracts\FormularioInsRepositoryInterface;
class UpdateFormularioInsHandler {
    public function __construct(private readonly FormularioInsRepositoryInterface $repository) {}
    public function handle(UpdateFormularioInsCommand $command): void {
        $this->repository->update($command->idFormins, array_filter([
            'id_imp'      => $command->idImp,
            'nombre'      => $command->nombre,
            'descripcion' => $command->descripcion,
        ], fn ($v) => $v !== null));
    }
}
