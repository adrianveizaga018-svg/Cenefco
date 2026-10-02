<?php

namespace App\Application\CompromisosCobro\Queries;

final readonly class GetResumenCompromisosCobroQuery
{
    public function __construct(
        public ?array $idImpPermitidos = null,
    ) {}
}
