<?php

declare(strict_types=1);

namespace App\Application\NotasPrensa\Queries;

final readonly class GetNotaPrensaByIdQuery
{
    public function __construct(
        public int|string $id,
    ) {}
}
