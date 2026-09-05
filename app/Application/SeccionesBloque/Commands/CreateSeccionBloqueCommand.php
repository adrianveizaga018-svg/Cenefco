<?php

namespace App\Application\SeccionesBloque\Commands;

final readonly class CreateSeccionBloqueCommand
{
    public function __construct(
        public int $idSeccionbloque,
        public ?int $idBloqueajustable,
        public ?string $titulo,
        public ?string $contenido,
        public int $idUsReg,
    ) {}
}
