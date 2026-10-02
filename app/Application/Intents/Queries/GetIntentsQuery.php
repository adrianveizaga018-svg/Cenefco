<?php

namespace App\Application\Intents\Queries;

final readonly class GetIntentsQuery
{
    public function __construct(
        public ?string $query,
        public ?string $dominio,
    ) {}
}
