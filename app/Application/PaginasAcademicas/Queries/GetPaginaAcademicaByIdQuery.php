<?php
namespace App\Application\PaginasAcademicas\Queries;
final readonly class GetPaginaAcademicaByIdQuery {
    public function __construct(public int $idPagina) {}
}
