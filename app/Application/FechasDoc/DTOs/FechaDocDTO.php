<?php

namespace App\Application\FechasDoc\DTOs;

final readonly class FechaDocDTO
{
    public function __construct(
        public int $id_fechadoc,
        public int $id_plandoc,
        public ?int $id_us_reg,
        public ?int $num_fechadoc,
        public ?string $nro_doc,
        public ?string $tipo_documento,
        public ?string $fecha_inicio,
        public ?string $fecha_fin,
        public ?int $obligatorio,
        public int $estado,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id_fechadoc:    (int) $model->id_fechadoc,
            id_plandoc:     (int) $model->id_plandoc,
            id_us_reg:      isset($model->id_us_reg) ? (int) $model->id_us_reg : null,
            num_fechadoc:   isset($model->num_fechadoc) ? (int) $model->num_fechadoc : null,
            nro_doc:        $model->nro_doc ?? null,
            tipo_documento: $model->tipo_documento ?? null,
            fecha_inicio:   $model->fecha_inicio ?? null,
            fecha_fin:      $model->fecha_fin ?? null,
            obligatorio:    isset($model->obligatorio) ? (int) $model->obligatorio : null,
            estado:         (int) $model->estado,
        );
    }
}
