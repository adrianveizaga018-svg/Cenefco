<?php

namespace App\Application\Inscripciones\DTOs;

final readonly class InscripcionDTO
{
    public function __construct(
        public int $id_ins,
        public ?int $id_us_reg,
        public ?string $fecha_ins,
        public int $id_us,
        public int $id_imp,
        public ?int $id_plan,
        public ?string $observacion_ins,
        public ?string $observacion,
        public ?string $periodo,
        public ?string $gestion,
        public int $estado,
        public ?string $fecha_reg,
        public ?string $estudiante_nombre,
        public ?string $estudiante_ci,
        public ?string $estudiante_email,
        public ?string $estudiante_celular,
        public ?string $materia_nombre,
        public ?string $materia_sigla,
        public ?string $paralelo,
        public ?string $docente_nombre,
        public int $cuotas_pagadas,
        public float $total_pagado,
        public ?int    $id_vendedor,
        public ?string $canal_venta,
        public ?array  $documentos,
        public ?float  $curso_costo_monto,
        public bool    $es_participante,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id_ins:            (int) $model->id_ins,
            id_us_reg:         isset($model->id_us_reg) ? (int) $model->id_us_reg : null,
            fecha_ins:         $model->fecha_ins ?? null,
            id_us:             (int) $model->id_us,
            id_imp:            (int) $model->id_imp,
            id_plan:           isset($model->id_plan) ? (int) $model->id_plan : null,
            observacion_ins:   $model->observacion_ins ?? null,
            observacion:       $model->observacion ?? null,
            periodo:           $model->periodo ?? null,
            gestion:           $model->gestion ?? null,
            estado:            (int) $model->estado,
            fecha_reg:         $model->fecha_reg ?? null,
            estudiante_nombre: $model->estudiante_nombre ?? null,
            estudiante_ci:     $model->estudiante_ci ?? null,
            estudiante_email:  $model->estudiante_email ?? null,
            estudiante_celular:$model->estudiante_celular ?? null,
            materia_nombre:    $model->materia_nombre ?? null,
            materia_sigla:     $model->materia_sigla ?? null,
            paralelo:          $model->paralelo ?? null,
            docente_nombre:    $model->docente_nombre ?? null,
            cuotas_pagadas:    (int) ($model->cuotas_pagadas ?? 0),
            total_pagado:      (float) ($model->total_pagado ?? 0),
            id_vendedor:       isset($model->id_vendedor) ? (int) $model->id_vendedor : null,
            canal_venta:       $model->canal_venta ?? null,
            documentos:        is_string($model->documentos ?? null) ? json_decode($model->documentos, true) : ($model->documentos ?? null),
            curso_costo_monto: isset($model->curso_costo_monto) ? (float) $model->curso_costo_monto : null,
            es_participante:   (bool) ($model->es_participante ?? false),
        );
    }
}
