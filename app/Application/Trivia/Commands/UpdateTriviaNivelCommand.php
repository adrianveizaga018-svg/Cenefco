<?php

namespace App\Application\Trivia\Commands;

final readonly class UpdateTriviaNivelCommand
{
    public function __construct(
        public int $id,
        public array $data,
    ) {}
}
