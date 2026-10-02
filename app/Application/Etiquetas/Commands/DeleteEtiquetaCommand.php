<?php

namespace App\Application\Etiquetas\Commands;

final readonly class DeleteEtiquetaCommand
{
    public function __construct(public int $id) {}
}
