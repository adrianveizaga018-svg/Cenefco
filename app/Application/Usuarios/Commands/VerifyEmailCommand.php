<?php

namespace App\Application\Usuarios\Commands;

final readonly class VerifyEmailCommand
{
    public function __construct(
        public int $userId,
    ) {}
}
