<?php

namespace App\Application\Areas\Commands;

final readonly class UpdateAreaCommand
{
    public function __construct(
        public int   $id,
        public array $data,
    ) {}
}
