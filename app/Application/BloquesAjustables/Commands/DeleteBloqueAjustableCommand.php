<?php

namespace App\Application\BloquesAjustables\Commands;

final readonly class DeleteBloqueAjustableCommand
{
    public function __construct(public int $idBloqueajustable) {}
}
