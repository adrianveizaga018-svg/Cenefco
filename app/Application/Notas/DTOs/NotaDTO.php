<?php

namespace App\Application\Notas\DTOs;

final readonly class NotaDTO
{
    public function __construct(
        public int $id_not,
        public ?int $id_us_reg,
        public ?string $periodo,
        public ?string $gestion,
        public int $id_imp,
        public int $id_us,
        public ?int $id_mat,
        public int $nota,
        public int $nota_seg,
        public ?string $paralelo,
        public int $mostrarcert_notas,
        public int $estado,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id_not:            (int) $model->id_not,
            id_us_reg:         isset($model->id_us_reg) ? (int) $model->id_us_reg : null,
            periodo:           $model->periodo ?? null,
            gestion:           $model->gestion ?? null,
            id_imp:            (int) $model->id_imp,
            id_us:             (int) $model->id_us,
            id_mat:            isset($model->id_mat) ? (int) $model->id_mat : null,
            nota:              (int) $model->nota,
            nota_seg:          (int) ($model->nota_seg ?? 0),
            paralelo:          $model->paralelo ?? null,
            mostrarcert_notas: (int) ($model->mostrarcert_notas ?? 1),
            estado:            (int) $model->estado,
        );
    }
}
