<?php

namespace App\Application\Carreras\Commands;

final readonly class DeleteCarreraCommand
{
    public function __construct(public int $id) {}
}
