<?php

namespace App\Application\TiposPrograma\Commands;

final readonly class UpdateTipoProgramaCommand
{
    public function __construct(
        public int $idTipoprograma,
        public ?string $nombreTipoprograma,
        public ?int $estado,
    ) {}
}
