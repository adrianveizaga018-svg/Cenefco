<?php

namespace App\Application\Comunicados\Queries;

final readonly class GetComunicadoBySlugQuery
{
    public function __construct(public string $slug) {}
}
