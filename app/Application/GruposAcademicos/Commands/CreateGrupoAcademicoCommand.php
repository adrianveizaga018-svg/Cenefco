<?php
namespace App\Application\GruposAcademicos\Commands;
final readonly class CreateGrupoAcademicoCommand {
    public function __construct(
        public int $idGrupo,
        public ?int $idTest,
        public ?string $nombre,
        public int $idUsReg,
    ) {}
}
