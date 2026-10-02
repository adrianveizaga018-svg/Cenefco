<?php

namespace App\Application\Revistas\Commands;

final readonly class DeleteRevistaCommand
{
    public function __construct(public int $id) {}
}
