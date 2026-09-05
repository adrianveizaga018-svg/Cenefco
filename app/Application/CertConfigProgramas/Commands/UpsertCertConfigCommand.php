<?php

namespace App\Application\CertConfigProgramas\Commands;

final readonly class UpsertCertConfigCommand
{
    public function __construct(
        public int $programa_id,
        public bool $activo,
        public ?string $titulo,
        public ?string $descripcion,
    ) {}
}
