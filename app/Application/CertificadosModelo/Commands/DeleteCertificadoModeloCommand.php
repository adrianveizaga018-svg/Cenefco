<?php
namespace App\Application\CertificadosModelo\Commands;
final readonly class DeleteCertificadoModeloCommand {
    public function __construct(public int $idCertmod) {}
}
