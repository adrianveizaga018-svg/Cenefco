<?php

namespace App\Application\FechasDoc\Commands;

final readonly class CreateFechaDocCommand
{
    public function __construct(
        public int $id_fechadoc,
        public int $id_plandoc,
        public ?int $id_us_reg,
        public ?int $num_fechadoc,
        public ?string $nro_doc,
        public ?string $tipo_documento,
        public ?string $fecha_inicio,
        public ?string $fecha_fin,
        public ?int $obligatorio,
        public int $estado,
    ) {}
}
