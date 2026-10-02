<?php

namespace App\Application\Formularios\Queries;

final readonly class GetFormularioByIdQuery
{
    public function __construct(public int $id) {}
}
