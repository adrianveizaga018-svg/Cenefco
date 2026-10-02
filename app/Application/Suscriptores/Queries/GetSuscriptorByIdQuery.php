<?php

declare(strict_types=1);

namespace App\Application\Suscriptores\Queries;

final readonly class GetSuscriptorByIdQuery
{
    public function __construct(
        public int|string $id,
    ) {}
}
