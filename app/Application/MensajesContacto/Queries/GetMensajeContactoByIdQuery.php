<?php

namespace App\Application\MensajesContacto\Queries;

final readonly class GetMensajeContactoByIdQuery
{
    public function __construct(public int $id) {}
}
