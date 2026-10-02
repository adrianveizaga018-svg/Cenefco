<?php

namespace App\Application\Inscripciones\Commands;

final readonly class RegistrarDocumentoCommand
{
    public function __construct(
        public int $id_ins,
        public int $id_fechadoc,
        public ?bool $dejo_documento_fisico,
        public ?string $documento_digital,
        public ?string $observacion_doc,
        public int $id_us_reg,
    ) {}
}
