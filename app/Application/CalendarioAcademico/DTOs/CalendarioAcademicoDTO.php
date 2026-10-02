<?php

declare(strict_types=1);

namespace App\Application\CalendarioAcademico\DTOs;

final readonly class CalendarioAcademicoDTO
{
    public function __construct(
        public int $id,
        public string $titulo,
        public ?string $descripcion,
        public ?string $tipo,
        public ?string $color,
        public ?int $programa_id,
        public ?string $nombre_programa,
        public ?int $vendedor_id,
        public ?string $vendedor_nombre,
        public ?string $pagina,
        public ?int $duracion_dias,
        public ?float $costo_inflado,
        public ?float $descuento,
        public ?float $precio_vip,
        public ?string $observaciones,
        public string $fecha_inicio,
        public ?string $fecha_fin,
        public bool $todo_el_dia,
        public bool $destacado,
        public bool $publico,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}

    public static function fromModel(object $m): self
    {
        return new self(
            id: $m->id,
            titulo: $m->titulo,
            descripcion: $m->descripcion,
            tipo: $m->tipo,
            color: $m->color,
            programa_id: $m->programa_id,
            nombre_programa: $m->nombre_programa ?? null,
            vendedor_id: $m->vendedor_id,
            vendedor_nombre: $m->vendedor_nombre ?? null,
            pagina: $m->pagina,
            duracion_dias: $m->duracion_dias,
            costo_inflado: $m->costo_inflado !== null ? (float) $m->costo_inflado : null,
            descuento: $m->descuento !== null ? (float) $m->descuento : null,
            precio_vip: $m->precio_vip !== null ? (float) $m->precio_vip : null,
            observaciones: $m->observaciones,
            fecha_inicio: $m->fecha_inicio?->toIso8601String(),
            fecha_fin: $m->fecha_fin?->toIso8601String(),
            todo_el_dia: (bool) $m->todo_el_dia,
            destacado: (bool) $m->destacado,
            publico: (bool) $m->publico,
            created_at: $m->created_at?->toIso8601String(),
            updated_at: $m->updated_at?->toIso8601String(),
        );
    }
}
