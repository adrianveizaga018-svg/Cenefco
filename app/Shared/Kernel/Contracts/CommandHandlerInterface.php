<?php

namespace App\Shared\Kernel\Contracts;

interface CommandHandlerInterface
{
    public function handle(CommandInterface $command): mixed;
}
