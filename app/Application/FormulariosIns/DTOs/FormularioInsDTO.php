<?php
namespace App\Application\FormulariosIns\DTOs;
final readonly class FormularioInsDTO {
    public function __construct(
        public int $idFormins,
        public ?int $idImp,
        public ?string $nombre,
        public ?string $descripcion,
        public int $estado,
    ) {}
    public static function fromRow(object $row): self {
        return new self(
            idFormins: $row->id_formins,
            idImp: $row->id_imp ?? null,
            nombre: $row->nombre ?? null,
            descripcion: $row->descripcion ?? null,
            estado: $row->estado,
        );
    }
}
