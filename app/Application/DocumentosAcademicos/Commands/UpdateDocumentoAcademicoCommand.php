<?php

namespace App\Application\DocumentosAcademicos\Commands;

final readonly class UpdateDocumentoAcademicoCommand
{
    public function __construct(
        public int $id,
        public ?int $id_fechapago,
        public ?int $id_fechadoc,
        public ?string $fecha_dejo_fisico,
        public ?int $dejo_documento_fisico,
        public ?string $documento_digital,
        public ?string $observacion_doc,
        public ?int $estado,
    ) {}
}
