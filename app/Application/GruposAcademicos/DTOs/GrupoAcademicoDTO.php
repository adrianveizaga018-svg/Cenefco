<?php
namespace App\Application\GruposAcademicos\DTOs;
final readonly class GrupoAcademicoDTO {
    public function __construct(
        public int $idGrupo,
        public ?int $idTest,
        public ?string $nombre,
        public int $estado,
    ) {}
    public static function fromRow(object $row): self {
        return new self(
            idGrupo: $row->id_grupo,
            idTest: $row->id_test ?? null,
            nombre: $row->nombre ?? null,
            estado: $row->estado,
        );
    }
}
