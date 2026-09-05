<?php
namespace App\Application\GruposAcademicos\Queries;
final readonly class GetGrupoAcademicoByIdQuery {
    public function __construct(public int $idGrupo) {}
}
