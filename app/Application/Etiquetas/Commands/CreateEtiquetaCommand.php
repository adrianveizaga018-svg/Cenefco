<?php

namespace App\Application\Etiquetas\Commands;

final readonly class CreateEtiquetaCommand
{
    public function __construct(public string $nombre) {}
}
