<?php

namespace App\Application\Expedidos\Queries;

final readonly class GetExpedidoByIdQuery
{
    public function __construct(public int $id) {}
}
