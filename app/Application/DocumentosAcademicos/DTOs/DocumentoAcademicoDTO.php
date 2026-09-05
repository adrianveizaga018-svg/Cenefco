<?php

namespace App\Application\DocumentosAcademicos\DTOs;

final readonly class DocumentoAcademicoDTO
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

    public static function fromModel(object $model): self
    {
        return new self(
            id_documento:         (int) $model->id_documento,
            id_us_reg:            isset($model->id_us_reg) ? (int) $model->id_us_reg : null,
            num_documento:        isset($model->num_documento) ? (int) $model->num_documento : null,
            id_us:                (int) $model->id_us,
            id_fechapago:         isset($model->id_fechapago) ? (int) $model->id_fechapago : null,
            id_fechadoc:          isset($model->id_fechadoc) ? (int) $model->id_fechadoc : null,
            fecha_dejo_fisico:    $model->fecha_dejo_fisico ?? null,
            dejo_documento_fisico: isset($model->dejo_documento_fisico) ? (int) $model->dejo_documento_fisico : null,
            documento_digital:    $model->documento_digital ?? null,
            observacion_doc:      $model->observacion_doc ?? null,
            estado:               (int) $model->estado,
        );
    }
}
