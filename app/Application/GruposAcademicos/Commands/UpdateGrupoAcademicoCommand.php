<?php
namespace App\Application\GruposAcademicos\Commands;
final readonly class UpdateGrupoAcademicoCommand {
    public function __construct(
        public int $idGrupo,
        public ?int $idTest,
        public ?string $nombre,
    ) {}
}
