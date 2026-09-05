<?php

namespace App\Application\Usuarios\Commands;

final readonly class ResendVerificationEmailCommand
{
    public function __construct(
        public int $userId,
    ) {}
}
