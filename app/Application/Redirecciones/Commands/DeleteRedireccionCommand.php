<?php

namespace App\Application\Redirecciones\Commands;

final readonly class DeleteRedireccionCommand
{
    public function __construct(public int $id) {}
}
