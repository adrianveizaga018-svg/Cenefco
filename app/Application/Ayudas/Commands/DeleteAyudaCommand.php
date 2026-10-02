<?php

namespace App\Application\Ayudas\Commands;

final readonly class DeleteAyudaCommand
{
    public function __construct(public int $id) {}
}
