<?php
namespace App\Application\PaginasAcademicas\Handlers;
use App\Application\PaginasAcademicas\Commands\DeletePaginaAcademicaCommand;
use App\Domain\PaginasAcademicas\Contracts\PaginaAcademicaRepositoryInterface;
class DeletePaginaAcademicaHandler {
    public function __construct(private readonly PaginaAcademicaRepositoryInterface $repository) {}
    public function handle(DeletePaginaAcademicaCommand $command): void {
        $this->repository->delete($command->idPagina);
    }
}
