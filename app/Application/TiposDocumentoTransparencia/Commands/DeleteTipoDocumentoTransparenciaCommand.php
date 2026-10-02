<?php

namespace App\Application\TiposDocumentoTransparencia\Commands;

final readonly class DeleteTipoDocumentoTransparenciaCommand
{
    public function __construct(public int $id) {}
}
