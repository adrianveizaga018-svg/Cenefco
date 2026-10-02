<?php

namespace App\Application\TiposDocumentoTransparencia\Queries;

final readonly class GetTipoDocumentoTransparenciaByIdQuery
{
    public function __construct(public int $id) {}
}
