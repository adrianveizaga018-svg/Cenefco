<?php
namespace App\Application\CertificadosModelo\Commands;
final readonly class CreateCertificadoModeloCommand {
    public function __construct(
        public int $idCertmod,
        public ?string $nombre,
        public ?string $descripcion,
        public int $idUsReg,
    ) {}
}
