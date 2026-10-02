<?php

namespace App\Application\SeccionesBloque\Commands;

final readonly class UpdateSeccionBloqueCommand
{
    public function __construct(
        public int $idSeccionbloque,
        public ?int $idBloqueajustable,
        public ?string $titulo,
        public ?string $contenido,
    ) {}
}
