<?php

namespace App\Application\DirectorioInstitucional\Queries;

final readonly class GetDirectorioByIdQuery
{
    public function __construct(public int $id) {}
}
