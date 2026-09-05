<?php

namespace App\Application\HistoriaMunicipio\Queries;

final readonly class GetHistoriaMunicipioByIdQuery
{
    public function __construct(public int $id) {}
}
