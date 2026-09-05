<?php

namespace App\Application\CompromisosCobro\DTOs;

final readonly class CompromisoCobroDTO
{
    public function __construct(
        public int     $id,
        public int     $id_ins,
        public int     $id_us,
        public int     $id_imp,
        public string  $fecha_compromiso,
        public ?string $hora_compromiso,
        public ?float  $monto_comprometido,
        public ?string $observacion,
        public string  $estado,
        public int     $veces_reprogramado,
        public int     $registrado_por,
        public ?string $notificado_at,
        public ?string $vencido_notificado_at,
        public ?string $created_at,
        public ?string $updated_at,
        public ?string $estudiante_nombre,
        public ?string $estudiante_ci,
        public ?string $curso_nombre,
        public ?string $registrado_por_nombre,
    ) {}

    public static function fromModel(object $m): self
    {
        return new self(
            id:                     (int) $m->id,
            id_ins:                 (int) $m->id_ins,
            id_us:                  (int) $m->id_us,
            id_imp:                 (int) $m->id_imp,
            fecha_compromiso:       (string) $m->fecha_compromiso,
            hora_compromiso:       isset($m->hora_compromiso) && $m->hora_compromiso !== null ? substr((string) $m->hora_compromiso, 0, 5) : null,
            monto_comprometido:     isset($m->monto_comprometido) && $m->monto_comprometido !== null ? (float) $m->monto_comprometido : null,
            observacion:            $m->observacion ?? null,
            estado:                 (string) $m->estado,
            veces_reprogramado:     (int) ($m->veces_reprogramado ?? 0),
            registrado_por:         (int) $m->registrado_por,
            notificado_at:          $m->notificado_at ? (string) $m->notificado_at : null,
            vencido_notificado_at:  $m->vencido_notificado_at ? (string) $m->vencido_notificado_at : null,
            created_at:             $m->created_at ? (string) $m->created_at : null,
            updated_at:             $m->updated_at ? (string) $m->updated_at : null,
            estudiante_nombre:      $m->estudiante_nombre ?? null,
            estudiante_ci:          $m->estudiante_ci ?? null,
            curso_nombre:           $m->curso_nombre ?? null,
            registrado_por_nombre:  $m->registrado_por_nombre ?? null,
        );
    }
}
