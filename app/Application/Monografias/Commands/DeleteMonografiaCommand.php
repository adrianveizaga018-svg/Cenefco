<?php

namespace App\Application\Monografias\Commands;

final readonly class DeleteMonografiaCommand
{
    public function __construct(public int $id) {}
}
