<?php

namespace App\Application\Pagos\Commands;

final readonly class ResolverDevolucionCommand
{
    public function __construct(
        public int     $id,
        public string  $estado,
        public ?string $notaRespuesta = null,
    ) {}
}
