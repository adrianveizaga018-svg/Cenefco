<?php

namespace App\Application\Expedidos\Commands;

final readonly class DeleteExpedidoCommand
{
    public function __construct(public int $id) {}
}
