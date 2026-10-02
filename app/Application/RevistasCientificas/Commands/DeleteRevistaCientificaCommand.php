<?php

namespace App\Application\RevistasCientificas\Commands;

final readonly class DeleteRevistaCientificaCommand
{
    public function __construct(public int $id) {}
}
