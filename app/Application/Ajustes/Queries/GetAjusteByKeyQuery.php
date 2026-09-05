<?php

namespace App\Application\Ajustes\Queries;

final readonly class GetAjusteByKeyQuery
{
    public function __construct(public string $key) {}
}
