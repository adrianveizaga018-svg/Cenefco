<?php

namespace App\Application\DescuentosPromociones\Commands;

final readonly class DeleteDescuentoPromocionCommand
{
    public function __construct(public int $id) {}
}
