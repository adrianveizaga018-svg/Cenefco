<?php

namespace App\Application\DocumentosTransparencia\Commands;

final readonly class UpdateDocumentoCommand
{
    public function __construct(public int $id, public array $data) {}
}
