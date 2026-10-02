<?php

namespace App\Application\TiposPrograma\Commands;

final readonly class DeleteTipoProgramaCommand
{
    public function __construct(public int $idTipoprograma) {}
}
