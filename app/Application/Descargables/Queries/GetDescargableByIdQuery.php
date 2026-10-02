<?php

declare(strict_types=1);

namespace App\Application\Descargables\Queries;

final readonly class GetDescargableByIdQuery
{
    public function __construct(
        public int|string $id,
    ) {}
}
