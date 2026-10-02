<?php

namespace App\Application\MensajesContacto\Commands;

final readonly class DeleteMensajeContactoCommand
{
    public function __construct(public int|array $ids) {}
}
