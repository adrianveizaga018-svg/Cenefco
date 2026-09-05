<?php

namespace App\Application\MediosPago\Commands;

final readonly class DeleteMedioPagoCommand
{
    public function __construct(public int $id) {}
}
