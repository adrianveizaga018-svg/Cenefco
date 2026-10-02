<?php

namespace App\Application\PreguntasFrecuentes\Commands;

final readonly class DeletePreguntaFrecuenteCommand
{
    public function __construct(public int|array $ids) {}
}
