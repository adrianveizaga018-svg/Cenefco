<?php
namespace App\Application\Historiales\Handlers;
use App\Application\Historiales\Commands\UpdateHistorialCommand;
use App\Domain\Historiales\Contracts\HistorialRepositoryInterface;
class UpdateHistorialHandler {
    public function __construct(private readonly HistorialRepositoryInterface $repository) {}
    public function handle(UpdateHistorialCommand $command): void {
        $this->repository->update($command->idHistorial, array_filter([
            'id_us'            => $command->idUs,
            'id_tiporeferencia'=> $command->idTiporeferencia,
            'id_tipohistorial' => $command->idTipohistorial,
            'descripcion'      => $command->descripcion,
        ], fn ($v) => $v !== null));
    }
}
