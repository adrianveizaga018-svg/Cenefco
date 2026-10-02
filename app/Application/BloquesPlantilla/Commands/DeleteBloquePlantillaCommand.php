<?php

namespace App\Application\BloquesPlantilla\Commands;

final readonly class DeleteBloquePlantillaCommand
{
    public function __construct(public int $idBloqueplantilla) {}
}
