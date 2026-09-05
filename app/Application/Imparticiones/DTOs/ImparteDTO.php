<?php

namespace App\Application\Imparticiones\DTOs;

final readonly class ImparteDTO
{
    public function __construct(
        public int $id_imp,
        public ?int $id_us_reg,
        public ?int $num_imp,
        public ?string $periodo,
        public ?string $gestion,
        public ?int $id_us,
        public ?int $id_mat,
        public ?string $paralelo,
        public ?string $cupo,
        public ?string $observacion_imp,
        public ?string $nro_resolucion_hcu,
        public ?int $id_moodle,
        public int $estado,
        public ?string $fecha_reg,
        public ?string $materia_nombre,
        public ?string $materia_sigla,
        public ?string $docente_nombre,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id_imp:              (int) $model->id_imp,
            id_us_reg:           isset($model->id_us_reg) ? (int) $model->id_us_reg : null,
            num_imp:             isset($model->num_imp) ? (int) $model->num_imp : null,
            periodo:             $model->periodo ?? null,
            gestion:             $model->gestion ?? null,
            id_us:               isset($model->id_us) ? (int) $model->id_us : null,
            id_mat:              isset($model->id_mat) ? (int) $model->id_mat : null,
            paralelo:            $model->paralelo ?? null,
            cupo:                $model->cupo ?? null,
            observacion_imp:     $model->observacion_imp ?? null,
            nro_resolucion_hcu:  $model->nro_resolucion_hcu ?? null,
            id_moodle:           isset($model->id_moodle) ? (int) $model->id_moodle : null,
            estado:              (int) $model->estado,
            fecha_reg:           $model->fecha_reg ?? null,
            materia_nombre:      $model->materia_nombre ?? null,
            materia_sigla:       $model->materia_sigla ?? null,
            docente_nombre:      $model->docente_nombre ?? null,
        );
    }
}
