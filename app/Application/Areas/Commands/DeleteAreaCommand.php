<?php

namespace App\Application\Areas\Commands;

final readonly class DeleteAreaCommand
{
    public function __construct(public int $id) {}
}
