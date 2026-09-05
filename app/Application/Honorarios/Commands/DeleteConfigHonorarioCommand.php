<?php

namespace App\Application\Honorarios\Commands;

final readonly class DeleteConfigHonorarioCommand
{
    public function __construct(public int $id_programa) {}
}
