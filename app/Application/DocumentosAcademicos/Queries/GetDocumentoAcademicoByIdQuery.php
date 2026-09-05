<?php

namespace App\Application\DocumentosAcademicos\Queries;

final readonly class GetDocumentoAcademicoByIdQuery
{
    public function __construct(public int $id) {}
}
