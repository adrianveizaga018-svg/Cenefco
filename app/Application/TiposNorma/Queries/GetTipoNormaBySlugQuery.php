<?php

namespace App\Application\TiposNorma\Queries;

use App\Shared\Kernel\Contracts\QueryInterface;

final readonly class GetTipoNormaBySlugQuery implements QueryInterface
{
    public function __construct(public string $slug) {}
}
