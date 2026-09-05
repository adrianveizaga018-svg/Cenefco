<?php

namespace App\Application\Inscripciones\Commands;

final readonly class MarcarParticipantesBulkCommand
{
    public function __construct(
        public array $idsIns,
        public int   $idUsReg,
    ) {}
}
