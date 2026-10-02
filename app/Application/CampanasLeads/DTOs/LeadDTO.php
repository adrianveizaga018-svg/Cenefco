<?php

namespace App\Application\CampanasLeads\DTOs;

final readonly class LeadDTO
{
    public function __construct(
        public int     $id,
        public int     $campana_lead_id,
        public string  $nombre,
        public string  $celular,
        public ?string $correo,
        public ?string $profesion,
        public string  $estado,
        public ?int    $vendedor_asignado_id,
        public ?int    $programa_id,
        public int     $total_seguimientos,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}

    public static function fromModel(object $m): self
    {
        return new self(
            id:                   $m->id,
            campana_lead_id:      $m->campana_lead_id,
            nombre:               $m->nombre,
            celular:              $m->celular,
            correo:               $m->correo ?? null,
            profesion:            $m->profesion ?? null,
            estado:               $m->estado ?? 'nuevo',
            vendedor_asignado_id: $m->vendedor_asignado_id ?? null,
            programa_id:          $m->programa_id ?? null,
            total_seguimientos:   $m->total_seguimientos ?? 0,
            created_at:           $m->created_at?->toIso8601String(),
            updated_at:           $m->updated_at?->toIso8601String(),
        );
    }
}
