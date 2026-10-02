<?php
namespace App\Application\GruposAcademicos\Commands;
final readonly class DeleteGrupoAcademicoCommand {
    public function __construct(public int $idGrupo) {}
}
