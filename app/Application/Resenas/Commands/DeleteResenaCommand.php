<?php

namespace App\Application\Resenas\Commands;

final readonly class DeleteResenaCommand
{
    public function __construct(public int $id) {}
}
