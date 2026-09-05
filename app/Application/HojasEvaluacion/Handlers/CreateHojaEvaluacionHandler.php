<?php
namespace App\Application\HojasEvaluacion\Handlers;
use App\Application\HojasEvaluacion\Commands\CreateHojaEvaluacionCommand;
use App\Application\HojasEvaluacion\DTOs\HojaEvaluacionDTO;
use App\Domain\HojasEvaluacion\Contracts\HojaEvaluacionRepositoryInterface;
class CreateHojaEvaluacionHandler {
    public function __construct(private readonly HojaEvaluacionRepositoryInterface $repository) {}
    public function handle(CreateHojaEvaluacionCommand $command): HojaEvaluacionDTO {
        $row = $this->repository->create([
            'id_hojaevaluacion' => $command->idHojaevaluacion,
            'id_us'             => $command->idUs,
            'id_us_tut'         => $command->idUsTut,
            'observacion'       => $command->observacion,
            'id_us_reg'         => $command->idUsReg,
            'fecha_reg'         => now(),
            'estado'            => 1,
        ]);
        return HojaEvaluacionDTO::fromRow($row);
    }
}
