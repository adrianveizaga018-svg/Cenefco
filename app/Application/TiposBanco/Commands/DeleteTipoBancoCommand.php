<?php

namespace App\Application\TiposBanco\Commands;

final readonly class DeleteTipoBancoCommand
{
    public function __construct(public int $id) {}
}
