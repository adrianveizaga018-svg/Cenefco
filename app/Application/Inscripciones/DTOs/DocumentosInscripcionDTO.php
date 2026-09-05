<?php

namespace App\Application\Inscripciones\DTOs;

final readonly class DocumentosInscripcionDTO
{
    public function __construct(
        public ?int $plan_id,
        public array $requeridos,
    ) {}
}
