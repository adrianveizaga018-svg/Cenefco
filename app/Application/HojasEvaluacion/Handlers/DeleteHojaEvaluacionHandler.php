<?php
namespace App\Application\HojasEvaluacion\Handlers;
use App\Application\HojasEvaluacion\Commands\DeleteHojaEvaluacionCommand;
use App\Domain\HojasEvaluacion\Contracts\HojaEvaluacionRepositoryInterface;
class DeleteHojaEvaluacionHandler {
    public function __construct(private readonly HojaEvaluacionRepositoryInterface $repository) {}
    public function handle(DeleteHojaEvaluacionCommand $command): void {
        $this->repository->delete($command->idHojaevaluacion);
    }
}
