<?php
namespace App\Application\CampanasLeads\DTOs;

final readonly class LeadSeguimientoDTO
{
    public function __construct(
        public int     $id,
        public int     $lead_id,
        public ?int    $vendedor_id,
        public string  $tipo,
        public string  $resultado,
        public ?string $nota,
        public ?string $proxima_accion,
        public ?string $fecha_proxima_accion,
        public ?string $vendedor_nombre,
        public ?string $created_at,
    ) {}

    public static function fromModel(object $m): self
    {
        return new self(
            id:                   $m->id,
            lead_id:              $m->lead_id,
            vendedor_id:          $m->vendedor_id,
            tipo:                 $m->tipo,
            resultado:            $m->resultado,
            nota:                 $m->nota ?? null,
            proxima_accion:       $m->proxima_accion ?? null,
            fecha_proxima_accion: $m->fecha_proxima_accion?->toDateString(),
            vendedor_nombre:      $m->vendedor_nombre ?? null,
            created_at:           $m->created_at?->toIso8601String(),
        );
    }
}
