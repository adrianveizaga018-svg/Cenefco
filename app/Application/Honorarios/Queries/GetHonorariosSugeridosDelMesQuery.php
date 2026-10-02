<?php

namespace App\Application\Honorarios\Queries;

final readonly class GetHonorariosSugeridosDelMesQuery
{
    public function __construct(
        public int $anio,
        public int $mes,
    ) {}
}
