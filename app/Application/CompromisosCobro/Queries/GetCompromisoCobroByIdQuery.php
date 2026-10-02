<?php

namespace App\Application\CompromisosCobro\Queries;

final readonly class GetCompromisoCobroByIdQuery
{
    public function __construct(public int $id) {}
}
