<?php
namespace App\Application\ModulosAcademicos\DTOs;
final readonly class ModuloAcademicoDTO {
    public function __construct(
        public int $idMod,
        public ?string $nombre,
        public ?string $descripcion,
        public ?int $posicion,
        public int $estado,
    ) {}
    public static function fromRow(object $row): self {
        return new self(
            idMod: $row->id_mod,
            nombre: $row->nombre ?? null,
            descripcion: $row->descripcion ?? null,
            posicion: $row->posicion ?? null,
            estado: $row->estado,
        );
    }
}
