<?php

namespace App\Application\DocumentosTransparencia\Queries;

final readonly class GetDocumentoByIdQuery
{
    public function __construct(public int $id) {}
}
