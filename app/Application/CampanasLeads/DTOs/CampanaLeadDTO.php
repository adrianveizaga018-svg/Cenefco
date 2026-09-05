<?php

namespace App\Application\CampanasLeads\DTOs;

final readonly class CampanaLeadDTO
{
    public function __construct(
        public int     $id,
        public string  $nombre,
        public ?string $descripcion,
        public string  $estado,
        public ?string $fecha_inicio,
        public ?string $fecha_fin,
        public int     $total_leads,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}

    public static function fromModel(object $m): self
    {
        return new self(
            id:           $m->id,
            nombre:       $m->nombre,
            descripcion:  $m->descripcion ?? null,
            estado:       $m->estado,
            fecha_inicio: $m->fecha_inicio?->toDateString(),
            fecha_fin:    $m->fecha_fin?->toDateString(),
            total_leads:  (int) ($m->leads_count ?? 0),
            created_at:   $m->created_at?->toIso8601String(),
            updated_at:   $m->updated_at?->toIso8601String(),
        );
    }
}
