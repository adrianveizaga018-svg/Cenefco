<?php

namespace App\Application\Fotos\Commands;

final readonly class DeleteFotoCommand
{
    public function __construct(public int $id) {}
}
