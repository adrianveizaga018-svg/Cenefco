<?php

namespace App\Application\CertConfigProgramas\Commands;

final readonly class CreateCertConfigItemCommand
{
    public function __construct(
        public int $config_id,
        public ?int $plantilla_id,
        public string $nombre_cert,
        public float $precio,
        public bool $es_gratuito,
        public int $orden,
    ) {}
}
