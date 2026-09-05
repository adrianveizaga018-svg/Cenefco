<?php

namespace App\Application\CampanasPublicidad\Commands;

final readonly class DeleteCampanaPublicidadCommand
{
    public function __construct(public int $id) {}
}
