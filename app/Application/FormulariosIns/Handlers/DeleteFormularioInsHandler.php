<?php
namespace App\Application\FormulariosIns\Handlers;
use App\Application\FormulariosIns\Commands\DeleteFormularioInsCommand;
use App\Domain\FormulariosIns\Contracts\FormularioInsRepositoryInterface;
class DeleteFormularioInsHandler {
    public function __construct(private readonly FormularioInsRepositoryInterface $repository) {}
    public function handle(DeleteFormularioInsCommand $command): void {
        $this->repository->delete($command->idFormins);
    }
}
