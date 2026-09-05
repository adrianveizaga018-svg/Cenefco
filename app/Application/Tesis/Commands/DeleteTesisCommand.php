<?php

namespace App\Application\Tesis\Commands;

final readonly class DeleteTesisCommand
{
    public function __construct(public int $id) {}
}
