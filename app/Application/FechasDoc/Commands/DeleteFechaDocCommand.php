<?php

namespace App\Application\FechasDoc\Commands;

final readonly class DeleteFechaDocCommand
{
    public function __construct(public int $id) {}
}
