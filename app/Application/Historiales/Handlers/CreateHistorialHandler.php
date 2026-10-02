<?php
namespace App\Application\Historiales\Handlers;
use App\Application\Historiales\Commands\CreateHistorialCommand;
use App\Application\Historiales\DTOs\HistorialDTO;
use App\Domain\Historiales\Contracts\HistorialRepositoryInterface;
class CreateHistorialHandler {
    public function __construct(private readonly HistorialRepositoryInterface $repository) {}
    public function handle(CreateHistorialCommand $command): HistorialDTO {
        $row = $this->repository->create([
            'id_historial'     => $command->idHistorial,
            'id_us'            => $command->idUs,
            'id_tiporeferencia'=> $command->idTiporeferencia,
            'id_tipohistorial' => $command->idTipohistorial,
            'descripcion'      => $command->descripcion,
            'id_us_reg'        => $command->idUsReg,
            'fecha_reg'        => now(),
            'estado'           => 1,
        ]);
        return HistorialDTO::fromRow($row);
    }
}
