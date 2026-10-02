<?php
namespace App\Application\FormatosHojaSolicitud\Handlers;
use App\Application\FormatosHojaSolicitud\Commands\UpdateFormatoHojaSolicitudCommand;
use App\Domain\FormatosHojaSolicitud\Contracts\FormatoHojaSolicitudRepositoryInterface;
class UpdateFormatoHojaSolicitudHandler {
    public function __construct(private readonly FormatoHojaSolicitudRepositoryInterface $repository) {}
    public function handle(UpdateFormatoHojaSolicitudCommand $command): void {
        $this->repository->update($command->idFormatoHojaSolicitud, array_filter([
            'nombre'      => $command->nombre,
            'descripcion' => $command->descripcion,
        ], fn ($v) => $v !== null));
    }
}
