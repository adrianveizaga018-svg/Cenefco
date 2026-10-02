<?php

declare(strict_types=1);

namespace App\Application\CalendarioAcademico\Commands;

final readonly class UpdateCalendarioAcademicoCommand
{
    public function __construct(
        public int|string $id,
        public ?string $titulo = null,
        public ?string $descripcion = null,
        public ?string $tipo = null,
        public ?string $color = null,
        public ?int $programa_id = null,
        public ?int $vendedor_id = null,
        public ?string $pagina = null,
        public ?int $duracion_dias = null,
        public ?float $costo_inflado = null,
        public ?float $descuento = null,
        public ?float $precio_vip = null,
        public ?string $observaciones = null,
        public ?string $fecha_inicio = null,
        public ?string $fecha_fin = null,
        public ?bool $todo_el_dia = null,
        public ?bool $destacado = null,
        public ?bool $publico = null,
    ) {}
}
