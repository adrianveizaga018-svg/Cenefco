<?php

namespace App\Application\Trivia\Commands;

final readonly class DeleteTriviaPreguntaCommand
{
    public function __construct(public int|array $id) {}
}
