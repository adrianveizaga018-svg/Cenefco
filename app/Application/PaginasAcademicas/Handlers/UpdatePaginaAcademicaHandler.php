<?php
namespace App\Application\PaginasAcademicas\Handlers;
use App\Application\PaginasAcademicas\Commands\UpdatePaginaAcademicaCommand;
use App\Domain\PaginasAcademicas\Contracts\PaginaAcademicaRepositoryInterface;
class UpdatePaginaAcademicaHandler {
    public function __construct(private readonly PaginaAcademicaRepositoryInterface $repository) {}
    public function handle(UpdatePaginaAcademicaCommand $command): void {
        $this->repository->update($command->idPagina, array_filter([
            'nombre'      => $command->nombre,
            'descripcion' => $command->descripcion,
        ], fn ($v) => $v !== null));
    }
}
