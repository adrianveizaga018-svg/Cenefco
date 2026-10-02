<?php

namespace App\Application\Trivia\Commands;

final readonly class DeleteTriviaCategoriaCommand
{
    public function __construct(public int|array $id) {}
}
