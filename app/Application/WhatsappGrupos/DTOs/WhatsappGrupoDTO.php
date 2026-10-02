<?php
namespace App\Application\WhatsappGrupos\DTOs;
final readonly class WhatsappGrupoDTO {
    public function __construct(
        public int $id,
        public int $imparte_id,
        public string $nombre,
        public string $enlace_invitacion,
        public ?int $capacidad_maxima,
        public int $miembros_actuales,
        public ?string $descripcion,
        public bool $activo,
        public int $orden,
        public ?string $fecha_expiracion_enlace,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}
    public static function fromModel(object $m): self {
        return new self(
            id: $m->id,
            imparte_id: (int)$m->imparte_id,
            nombre: $m->nombre,
            enlace_invitacion: $m->enlace_invitacion,
            capacidad_maxima: isset($m->capacidad_maxima) ? (int)$m->capacidad_maxima : null,
            miembros_actuales: (int)($m->miembros_actuales ?? 0),
            descripcion: $m->descripcion ?? null,
            activo: (bool)($m->activo ?? false),
            orden: (int)($m->orden ?? 0),
            fecha_expiracion_enlace: $m->fecha_expiracion_enlace?->toIso8601String(),
            created_at: $m->created_at?->toIso8601String(),
            updated_at: $m->updated_at?->toIso8601String(),
        );
    }
}
