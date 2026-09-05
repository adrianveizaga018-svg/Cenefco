<?php

namespace App\Application\Acreditaciones\Queries;

final readonly class GetAcreditacionByIdQuery
{
    public function __construct(
        public int $id,
    ) {}
}
