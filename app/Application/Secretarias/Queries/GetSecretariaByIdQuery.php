<?php

namespace App\Application\Secretarias\Queries;

final readonly class GetSecretariaByIdQuery
{
    public function __construct(public int $id) {}
}
