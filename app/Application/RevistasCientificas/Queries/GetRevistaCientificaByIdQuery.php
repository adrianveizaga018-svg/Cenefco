<?php

namespace App\Application\RevistasCientificas\Queries;

final readonly class GetRevistaCientificaByIdQuery
{
    public function __construct(public int $id) {}
}
