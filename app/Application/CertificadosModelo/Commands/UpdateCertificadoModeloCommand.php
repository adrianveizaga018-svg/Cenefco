<?php
namespace App\Application\CertificadosModelo\Commands;
final readonly class UpdateCertificadoModeloCommand {
    public function __construct(
        public int $idCertmod,
        public ?string $nombre,
        public ?string $descripcion,
    ) {}
}
