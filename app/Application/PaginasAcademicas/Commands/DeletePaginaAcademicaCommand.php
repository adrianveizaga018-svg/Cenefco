<?php
namespace App\Application\PaginasAcademicas\Commands;
final readonly class DeletePaginaAcademicaCommand {
    public function __construct(public int $idPagina) {}
}
