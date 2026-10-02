<?php

namespace App\Shared\Kernel\Contracts;

interface QueryHandlerInterface
{
    public function handle(QueryInterface $query): mixed;
}
