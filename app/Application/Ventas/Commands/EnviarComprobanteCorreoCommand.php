<?php

namespace App\Application\Ventas\Commands;

final readonly class EnviarComprobanteCorreoCommand
{
    public function __construct(
        public int $idIns,
        public ?string $email = null,
    ) {}
}
