<?php

namespace App\Application\MediosPago\Queries;

final readonly class GetMedioPagoByIdQuery
{
    public function __construct(public int $id) {}
}
