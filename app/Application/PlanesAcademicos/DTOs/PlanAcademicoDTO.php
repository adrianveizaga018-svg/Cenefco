<?php

namespace App\Application\PlanesAcademicos\DTOs;

final readonly class PlanAcademicoDTO
{
    public function __construct(
        public int $id_plan,
        public ?int $id_us_reg,
        public string $titulo,
        public ?string $titulo_plan,
        public ?string $convenio,
        public ?int $convenio_id,
        public ?string $anio,
        public ?string $numero_resolucion,
        public ?string $costo,
        public ?string $nro_cuotas,
        public ?string $descuento,
        public ?string $costo_por_cuota,
        public ?int $id_catplan,
        public int $estado,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id_plan:           (int) $model->id_plan,
            id_us_reg:         isset($model->id_us_reg) ? (int) $model->id_us_reg : null,
            titulo:            $model->titulo,
            titulo_plan:       $model->titulo_plan ?? null,
            convenio:          $model->convenio ?? null,
            convenio_id:       isset($model->convenio_id) ? (int) $model->convenio_id : null,
            anio:              $model->anio ?? null,
            numero_resolucion: $model->numero_resolucion ?? null,
            costo:             $model->costo ?? null,
            nro_cuotas:        $model->nro_cuotas ?? null,
            descuento:         $model->descuento ?? null,
            costo_por_cuota:   $model->costo_por_cuota ?? null,
            id_catplan:        isset($model->id_catplan) ? (int) $model->id_catplan : null,
            estado:            (int) $model->estado,
        );
    }
}
