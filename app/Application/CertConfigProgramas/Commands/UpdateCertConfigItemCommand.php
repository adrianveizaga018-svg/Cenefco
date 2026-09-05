<?php

namespace App\Application\CertConfigProgramas\Commands;

final readonly class UpdateCertConfigItemCommand
{
    public function __construct(
        public int $id,
        public ?int $plantilla_id,
        public string $nombre_cert,
        public float $precio,
        public bool $es_gratuito,
        public int $orden,
        public bool $activo,
    ) {}
}
