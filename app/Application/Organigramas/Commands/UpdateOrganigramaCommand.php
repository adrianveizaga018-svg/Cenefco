<?php

namespace App\Application\Organigramas\Commands;

final readonly class UpdateOrganigramaCommand
{
    public function __construct(public int $id, public array $data) {}
}
