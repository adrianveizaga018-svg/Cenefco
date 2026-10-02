<?php
namespace App\Application\FormatosHojaSolicitud\Handlers;
use App\Application\FormatosHojaSolicitud\Commands\DeleteFormatoHojaSolicitudCommand;
use App\Domain\FormatosHojaSolicitud\Contracts\FormatoHojaSolicitudRepositoryInterface;
class DeleteFormatoHojaSolicitudHandler {
    public function __construct(private readonly FormatoHojaSolicitudRepositoryInterface $repository) {}
    public function handle(DeleteFormatoHojaSolicitudCommand $command): void {
        $this->repository->delete($command->idFormatoHojaSolicitud);
    }
}
