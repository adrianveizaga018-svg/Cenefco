<?php
namespace App\Application\CertificadosModelo\DTOs;
final readonly class CertificadoModeloDTO {
    public function __construct(
        public int $idCertmod,
        public ?string $nombre,
        public ?string $descripcion,
        public int $estado,
    ) {}
    public static function fromRow(object $row): self {
        return new self(
            idCertmod: $row->id_certmod,
            nombre: $row->nombre ?? null,
            descripcion: $row->descripcion ?? null,
            estado: $row->estado,
        );
    }
}
