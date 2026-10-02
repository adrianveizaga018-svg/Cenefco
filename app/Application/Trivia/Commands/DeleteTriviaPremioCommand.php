<?php

namespace App\Application\Trivia\Commands;

final readonly class DeleteTriviaPremioCommand
{
    public function __construct(public int $id) {}
}
