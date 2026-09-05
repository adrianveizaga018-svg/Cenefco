<?php

namespace App\Application\Inscripciones\Commands;

final readonly class UpdateInscripcionCommand
{
    public function __construct(
        public int $id,
        public ?string $fecha_ins,
        public ?int $id_plan,
        public bool $clearIdPlan,
        public ?string $observacion_ins,
        public ?string $observacion,
        public ?int $estado,
        public ?array $documentos = null,
        public ?array $campos_extra = null,
    ) {}
}
