<?php
namespace App\Application\Historiales\Handlers;
use App\Application\Historiales\Commands\DeleteHistorialCommand;
use App\Domain\Historiales\Contracts\HistorialRepositoryInterface;
class DeleteHistorialHandler {
    public function __construct(private readonly HistorialRepositoryInterface $repository) {}
    public function handle(DeleteHistorialCommand $command): void {
        $this->repository->delete($command->idHistorial);
    }
}
