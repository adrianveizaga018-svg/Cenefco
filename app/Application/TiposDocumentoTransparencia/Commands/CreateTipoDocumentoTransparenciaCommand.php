<?php

namespace App\Application\TiposDocumentoTransparencia\Commands;

final readonly class CreateTipoDocumentoTransparenciaCommand
{
    public function __construct(public string $nombre, public bool $activo = true) {}
}
