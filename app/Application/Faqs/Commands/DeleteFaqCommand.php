<?php

namespace App\Application\Faqs\Commands;

final readonly class DeleteFaqCommand
{
    public function __construct(public int $id) {}
}
