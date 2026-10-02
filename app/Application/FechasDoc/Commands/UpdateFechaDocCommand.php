<?php

namespace App\Application\FechasDoc\Commands;

final readonly class UpdateFechaDocCommand
{
    public function __construct(
        public int $id,
        public ?string $nro_doc,
        public ?string $tipo_documento,
        public ?string $fecha_inicio,
        public ?string $fecha_fin,
        public ?int $obligatorio,
        public ?int $estado,
    ) {}
}
