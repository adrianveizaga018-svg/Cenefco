<?php

namespace App\Application\Normas\Commands;

final readonly class UpdateNormaCommand
{
    public function __construct(public int $id, public array $data) {}
}
