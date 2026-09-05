<?php

namespace App\Application\Autoridades\Commands;

final readonly class DeleteAutoridadCommand
{
    public function __construct(public int $id) {}
}
