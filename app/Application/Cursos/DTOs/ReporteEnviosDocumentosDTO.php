<?php

namespace App\Application\Cursos\DTOs;

final readonly class ReporteEnviosDocumentosDTO
{
    public function __construct(
        public ?string $fecha_inicio,
        public ?string $fecha_fin,
        public int $total,
        public array $envios,
    ) {}
}
