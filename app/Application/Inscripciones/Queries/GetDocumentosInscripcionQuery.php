<?php

namespace App\Application\Inscripciones\Queries;

final readonly class GetDocumentosInscripcionQuery
{
    public function __construct(public int $idIns) {}
}
