<?php

namespace App\Application\Inscripciones\Commands;

final readonly class MarcarParticipanteCommand
{
    public function __construct(
        public int $idIns,
        public int $idUsReg,
    ) {}
}
