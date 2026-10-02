<?php

declare(strict_types=1);

namespace App\Application\Suscriptores\DTOs;

final readonly class SuscriptorDTO
{
    public function __construct(
        public int $id,
        public string $email,
        public ?string $nombre,
        public bool $confirmado,
        public ?string $token_confirmacion,
        public bool $activo,
        public ?string $origen,
        public ?string $utm_source,
        public ?string $utm_medium,
        public ?string $utm_campaign,
        public ?string $ip_origen,
        public ?string $fecha_confirmacion,
        public ?string $created_at,
        public ?string $updated_at,
        public ?string $deleted_at,
    ) {}

    public static function fromModel(object $m): self
    {
        return new self(
            id: $m->id,
            email: $m->email,
            nombre: $m->nombre,
            confirmado: (bool) $m->confirmado,
            token_confirmacion: $m->token_confirmacion,
            activo: (bool) $m->activo,
            origen: $m->origen,
            utm_source: $m->utm_source,
            utm_medium: $m->utm_medium,
            utm_campaign: $m->utm_campaign,
            ip_origen: $m->ip_origen,
            fecha_confirmacion: $m->fecha_confirmacion?->toIso8601String(),
            created_at: $m->created_at?->toIso8601String(),
            updated_at: $m->updated_at?->toIso8601String(),
            deleted_at: $m->deleted_at?->toIso8601String(),
        );
    }
}
