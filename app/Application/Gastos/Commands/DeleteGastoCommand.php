<?php

namespace App\Application\Gastos\Commands;

final readonly class DeleteGastoCommand
{
    public function __construct(public int $id) {}
}
