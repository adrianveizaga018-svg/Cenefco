<?php

declare(strict_types=1);

namespace App\Application\NotasPrensa\Commands;

final readonly class DeleteNotaPrensaCommand
{
    public function __construct(
        public int|string $id,
    ) {}
}
