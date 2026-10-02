<?php
namespace App\Application\HojasEvaluacion\Handlers;
use App\Application\HojasEvaluacion\Commands\UpdateHojaEvaluacionCommand;
use App\Domain\HojasEvaluacion\Contracts\HojaEvaluacionRepositoryInterface;
class UpdateHojaEvaluacionHandler {
    public function __construct(private readonly HojaEvaluacionRepositoryInterface $repository) {}
    public function handle(UpdateHojaEvaluacionCommand $command): void {
        $this->repository->update($command->idHojaevaluacion, array_filter([
            'id_us'      => $command->idUs,
            'id_us_tut'  => $command->idUsTut,
            'observacion'=> $command->observacion,
        ], fn ($v) => $v !== null));
    }
}
