<?php

namespace App\Application\TiposPrograma\Commands;

final readonly class CreateTipoProgramaCommand
{
    public function __construct(
        public int $idTipoprograma,
        public string $nombreTipoprograma,
        public int $idUsReg,
        public ?int $estado,
    ) {}
}
