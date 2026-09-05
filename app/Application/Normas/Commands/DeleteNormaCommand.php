<?php

namespace App\Application\Normas\Commands;

final readonly class DeleteNormaCommand
{
    public function __construct(public int $id) {}
}
