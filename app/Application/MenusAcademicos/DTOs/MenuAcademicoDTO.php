<?php
namespace App\Application\MenusAcademicos\DTOs;
final readonly class MenuAcademicoDTO {
    public function __construct(
        public int $idMen,
        public ?int $idMod,
        public ?string $nombre,
        public ?string $descripcion,
        public int $estado,
    ) {}
    public static function fromRow(object $row): self {
        return new self(
            idMen: $row->id_men,
            idMod: $row->id_mod ?? null,
            nombre: $row->nombre ?? null,
            descripcion: $row->descripcion ?? null,
            estado: $row->estado,
        );
    }
}
