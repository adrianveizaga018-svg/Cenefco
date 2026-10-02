<?php

namespace App\Application\Acreditaciones\Commands;

final readonly class DeleteAcreditacionCommand
{
    public function __construct(
        public int $id,
    ) {}
}
