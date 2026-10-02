<?php

declare(strict_types=1);

namespace App\Application\CalendarioAcademico\DTOs;

use Carbon\Carbon;

final readonly class CursoVigenteDTO
{
    public function __construct(
        public int $id_imp,
        public ?string $periodo,
        public ?string $gestion,
        public ?string $materia_nombre,
        public ?string $programa_nombre,
        public ?string $programa_slug,
        public string $fecha_inicio,
        public string $fecha_fin,
        public int $dias_restantes,
        public ?string $cupo,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id_imp: (int) $row->id_imp,
            periodo: $row->periodo,
            gestion: $row->gestion,
            materia_nombre: $row->materia_nombre,
            programa_nombre: $row->nombre_programa,
            programa_slug: $row->programa_slug,
            fecha_inicio: (string) $row->imparte_fecha_inicio,
            fecha_fin: (string) $row->imparte_fecha_fin,
            dias_restantes: (int) Carbon::now()->startOfDay()->diffInDays(Carbon::parse($row->imparte_fecha_fin)->startOfDay(), false),
            cupo: $row->cupo,
        );
    }
}
