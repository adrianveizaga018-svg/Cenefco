<?php

namespace App\Application\TiposDocumentoTransparencia\Commands;

final readonly class UpdateTipoDocumentoTransparenciaCommand
{
    public function __construct(public int $id, public array $data) {}
}
