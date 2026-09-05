<?php

declare(strict_types=1);

namespace App\Application\Descargables\Commands;

final readonly class DeleteDescargableCommand
{
    public function __construct(
        public int|string $id,
    ) {}
}
