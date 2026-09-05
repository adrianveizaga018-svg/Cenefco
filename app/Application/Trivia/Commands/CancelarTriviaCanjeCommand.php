<?php

namespace App\Application\Trivia\Commands;

final readonly class CancelarTriviaCanjeCommand
{
    public function __construct(
        public int $id,
        public int $canceladoPor,
        public ?string $nota,
    ) {}
}
