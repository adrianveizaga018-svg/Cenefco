<?php

namespace App\Application\DocumentosAcademicos\Commands;

final readonly class DeleteDocumentoAcademicoCommand
{
    public function __construct(public int $id) {}
}
