<?php

namespace App\Application\Formularios\Queries;

final readonly class GetFormularioBySlugQuery
{
    public function __construct(public string $slug) {}
}
