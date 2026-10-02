<?php

namespace App\Application\Trivia\Commands;

final readonly class UpdateTriviaCategoriaCommand
{
    public function __construct(
        public int $id,
        public array $data,
    ) {}
}
