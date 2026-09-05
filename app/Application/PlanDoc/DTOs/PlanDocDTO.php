<?php
namespace App\Application\PlanDoc\DTOs;
final readonly class PlanDocDTO {
    public function __construct(
        public int $idPlandoc,
        public ?string $nombre,
        public ?string $descripcion,
        public int $estado,
    ) {}
    public static function fromRow(object $row): self {
        return new self(
            idPlandoc: $row->id_plandoc,
            nombre: $row->nombre ?? null,
            descripcion: $row->descripcion ?? null,
            estado: $row->estado,
        );
    }
}
