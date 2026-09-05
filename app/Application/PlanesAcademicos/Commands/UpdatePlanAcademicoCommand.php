<?php

namespace App\Application\PlanesAcademicos\Commands;

final readonly class UpdatePlanAcademicoCommand
{
    public function __construct(
        public int $id,
        public ?string $titulo,
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
        public ?int $estado,
    ) {}
}
