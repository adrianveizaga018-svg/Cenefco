<?php

namespace App\Application\Certificados\Commands;

final readonly class UpdateCertificadoCommand
{
    public function __construct(
        public int     $id,
        public ?string $nombre_en_certificado    = null,
        public ?string $programa_en_certificado  = null,
        public ?string $qr_url                   = null,
        public ?string $archivo_url              = null,
        public ?string $archivo_miniatura_url     = null,
        public ?string $estado                   = null,
        public ?string $motivo_anulacion         = null,
        public ?int    $anulado_por              = null,
    ) {}
}
