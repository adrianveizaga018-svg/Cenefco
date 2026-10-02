<?php

namespace App\Application\Trivia\Commands;

final readonly class UpdateTriviaPreguntaCommand
{
    public function __construct(
        public int $id,
        public array $data,
        public ?array $opciones = null,
    ) {}
}
