<?php

namespace App\Application\Secretarias\Commands;

final readonly class DeleteSecretariaCommand
{
    public function __construct(public int $id) {}
}
