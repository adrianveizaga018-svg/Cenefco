<?php

namespace App\Application\DocumentosTransparencia\Commands;

final readonly class DeleteDocumentoCommand
{
    public function __construct(public int $id) {}
}
