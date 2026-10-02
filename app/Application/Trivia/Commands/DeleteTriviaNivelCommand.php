<?php

namespace App\Application\Trivia\Commands;

final readonly class DeleteTriviaNivelCommand
{
    public function __construct(public int|array $id) {}
}
