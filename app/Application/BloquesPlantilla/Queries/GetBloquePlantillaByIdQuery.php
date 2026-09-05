<?php

namespace App\Application\BloquesPlantilla\Queries;

final readonly class GetBloquePlantillaByIdQuery
{
    public function __construct(public int $idBloqueplantilla) {}
}
