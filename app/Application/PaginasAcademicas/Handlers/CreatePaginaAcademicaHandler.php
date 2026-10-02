<?php
namespace App\Application\PaginasAcademicas\Handlers;
use App\Application\PaginasAcademicas\Commands\CreatePaginaAcademicaCommand;
use App\Application\PaginasAcademicas\DTOs\PaginaAcademicaDTO;
use App\Domain\PaginasAcademicas\Contracts\PaginaAcademicaRepositoryInterface;
class CreatePaginaAcademicaHandler {
    public function __construct(private readonly PaginaAcademicaRepositoryInterface $repository) {}
    public function handle(CreatePaginaAcademicaCommand $command): PaginaAcademicaDTO {
        $row = $this->repository->create([
            'id_pagina'   => $command->idPagina,
            'nombre'      => $command->nombre,
            'descripcion' => $command->descripcion,
            'id_us_reg'   => $command->idUsReg,
            'fecha_reg'   => now(),
            'estado'      => 1,
        ]);
        return PaginaAcademicaDTO::fromRow($row);
    }
}
