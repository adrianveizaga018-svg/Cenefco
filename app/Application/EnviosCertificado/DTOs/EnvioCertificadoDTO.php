<?php

namespace App\Application\EnviosCertificado\DTOs;

final readonly class EnvioCertificadoDTO
{
    public function __construct(
        public int     $id,
        public int     $id_ins,
        public string  $departamento,
        public string  $ciudad_destino,
        public string  $fecha_envio,
        public string  $imagen_guia,
        public ?string $aclaraciones,
        public string  $estado,
        public ?string $agencia,
        public ?string $nro_seguimiento,
        public ?string $fecha_entrega,
        public bool    $notificado,
        public ?float  $costo,
        public ?int    $id_us_reg,
        public ?int    $enviado_por,
        public ?string $enviado_at,
        public ?string $created_at,
        // Relaciones opcionales enriquecidas
        public ?string $estudiante_nombre = null,
        public ?string $estudiante_ci     = null,
        public ?string $estudiante_celular = null,
        public ?string $programa_nombre   = null,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id:               (int)    $model->id,
            id_ins:           (int)    $model->id_ins,
            departamento:              $model->departamento    ?? '',
            ciudad_destino:            $model->ciudad_destino,
            fecha_envio:      substr($model->fecha_envio ?? '', 0, 10),
            imagen_guia:               $model->imagen_guia,
            aclaraciones:              $model->aclaraciones    ?? null,
            estado:                    $model->estado          ?? 'pendiente',
            agencia:                   $model->agencia         ?? null,
            nro_seguimiento:           $model->nro_seguimiento ?? null,
            fecha_entrega:             isset($model->fecha_entrega) ? substr((string) $model->fecha_entrega, 0, 10) : null,
            notificado:       (bool)  ($model->notificado      ?? false),
            costo:            isset($model->costo) && $model->costo !== null ? (float) $model->costo : null,
            id_us_reg:        isset($model->id_us_reg)   ? (int) $model->id_us_reg   : null,
            enviado_por:      isset($model->enviado_por) ? (int) $model->enviado_por : null,
            enviado_at:       is_string($model->enviado_at ?? null)
                                  ? $model->enviado_at
                                  : ($model->enviado_at?->toIso8601String() ?? null),
            created_at:       is_string($model->created_at ?? null)
                                  ? $model->created_at
                                  : ($model->created_at?->toIso8601String() ?? null),
            estudiante_nombre: $model->estudiante_nombre ?? null,
            estudiante_ci:     $model->estudiante_ci     ?? null,
            estudiante_celular: $model->estudiante_celular ?? null,
            programa_nombre:   $model->programa_nombre   ?? null,
        );
    }
}
