<?php

namespace App\Application\Convenios\Commands;

final readonly class DeleteConvenioCommand
{
    public function __construct(
        public int $id,
    ) {}
}
