<?php

namespace App\Application\Trivia\Queries;

final readonly class GetTriviaRankingQuery
{
    public function __construct(public int $limite = 20) {}
}
