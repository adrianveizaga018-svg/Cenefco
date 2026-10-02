<?php

namespace App\Application\Intents\Queries;

final readonly class GetIntentByIdQuery
{
    public function __construct(public int $id) {}
}
