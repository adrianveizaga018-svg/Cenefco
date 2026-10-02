<?php

namespace App\Application\Intents\Commands;

final readonly class DeleteIntentCommand
{
    public function __construct(public int $id) {}
}
