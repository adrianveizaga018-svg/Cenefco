<?php

namespace App\Application\Secretarias\Commands;

final readonly class UpdateSecretariaCommand
{
    public function __construct(public int $id, public array $data) {}
}
