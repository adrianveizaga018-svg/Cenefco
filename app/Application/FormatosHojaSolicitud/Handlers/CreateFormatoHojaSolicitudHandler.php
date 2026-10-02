<?php
namespace App\Application\FormatosHojaSolicitud\Handlers;
use App\Application\FormatosHojaSolicitud\Commands\CreateFormatoHojaSolicitudCommand;
use App\Application\FormatosHojaSolicitud\DTOs\FormatoHojaSolicitudDTO;
use App\Domain\FormatosHojaSolicitud\Contracts\FormatoHojaSolicitudRepositoryInterface;
class CreateFormatoHojaSolicitudHandler {
    public function __construct(private readonly FormatoHojaSolicitudRepositoryInterface $repository) {}
    public function handle(CreateFormatoHojaSolicitudCommand $command): FormatoHojaSolicitudDTO {
        $row = $this->repository->create([
            'id_formato_hoja_solicitud' => $command->idFormatoHojaSolicitud,
            'nombre'                    => $command->nombre,
            'descripcion'               => $command->descripcion,
            'id_us_reg'                 => $command->idUsReg,
            'fecha_reg'                 => now(),
            'estado'                    => 1,
        ]);
        return FormatoHojaSolicitudDTO::fromRow($row);
    }
}
