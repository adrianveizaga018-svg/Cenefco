<?php

namespace App\Application\Secretarias\Queries;

final readonly class GetSecretariaBySlugQuery
{
    public function __construct(public string $slug) {}
}
