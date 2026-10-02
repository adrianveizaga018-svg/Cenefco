<?php

namespace App\Application\DocumentosAcademicos\Commands;

final readonly class CreateDocumentoAcademicoCommand
{
    public function __construct(
        public int $id_documento,
        public ?int $id_us_reg,
        public ?int $num_documento,
        public int $id_us,
        public ?int $id_fechapago,
        public ?int $id_fechadoc,
        public ?string $fecha_dejo_fisico,
        public ?int $dejo_documento_fisico,
        public ?string $documento_digital,
        public ?string $observacion_doc,
        public int $estado,
    ) {}
}
