<?php

namespace App\Application\TiposNorma\Queries;

use App\Shared\Kernel\Contracts\QueryInterface;

final readonly class GetTipoNormaByIdQuery implements QueryInterface
{
    public function __construct(public int $id) {}
}
