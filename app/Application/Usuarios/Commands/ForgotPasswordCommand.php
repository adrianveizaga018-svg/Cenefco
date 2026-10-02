<?php

namespace App\Application\Usuarios\Commands;

class ForgotPasswordCommand
{
    public function __construct(public readonly string $email) {}
}
