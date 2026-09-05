<?php

namespace App\Application\Etiquetas\Queries;

final readonly class GetEtiquetaBySlugQuery
{
    public function __construct(public string $slug) {}
}
