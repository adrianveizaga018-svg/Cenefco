<?php

namespace App\Application\Etiquetas\Queries;

final readonly class GetEtiquetaByIdQuery
{
    public function __construct(public int $id) {}
}
