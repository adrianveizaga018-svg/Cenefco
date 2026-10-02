<?php

namespace App\Application\CompromisosCobro\Queries;

final readonly class GetCompromisoAbiertoPorInscripcionQuery
{
    public function __construct(public int $idIns) {}
}
