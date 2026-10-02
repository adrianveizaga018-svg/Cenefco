<?php
namespace App\Application\Historiales\DTOs;
final readonly class HistorialDTO {
    public function __construct(
        public int $idHistorial,
        public ?int $idUs,
        public ?int $idTiporeferencia,
        public ?int $idTipohistorial,
        public ?string $descripcion,
        public int $estado,
    ) {}
    public static function fromRow(object $row): self {
        return new self(
            idHistorial: $row->id_historial,
            idUs: $row->id_us ?? null,
            idTiporeferencia: $row->id_tiporeferencia ?? null,
            idTipohistorial: $row->id_tipohistorial ?? null,
            descripcion: $row->descripcion ?? null,
            estado: $row->estado,
        );
    }
}
