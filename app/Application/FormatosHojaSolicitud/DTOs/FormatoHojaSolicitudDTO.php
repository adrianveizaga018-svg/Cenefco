<?php
namespace App\Application\FormatosHojaSolicitud\DTOs;
final readonly class FormatoHojaSolicitudDTO {
    public function __construct(
        public int $idFormatoHojaSolicitud,
        public ?string $nombre,
        public ?string $descripcion,
        public int $estado,
    ) {}
    public static function fromRow(object $row): self {
        return new self(
            idFormatoHojaSolicitud: $row->id_formato_hoja_solicitud,
            nombre: $row->nombre ?? null,
            descripcion: $row->descripcion ?? null,
            estado: $row->estado,
        );
    }
}
