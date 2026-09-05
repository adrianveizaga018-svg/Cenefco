<?php

namespace App\Application\Organigramas\Commands;

final readonly class DeleteOrganigramaCommand
{
    public function __construct(public int $id) {}
}
