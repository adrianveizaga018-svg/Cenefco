<?php

namespace App\Application\CertPlantillaCampos\DTOs;

final readonly class CertPlantillaCampoDTO
{
    public function __construct(
        public int     $id,
        public int     $plantilla_id,
        public string  $clave,
        public string  $etiqueta,
        public string  $tipo,
        public float   $pos_x_pct,
        public float   $pos_y_pct,
        public ?float  $ancho_pct,
        public ?float  $alto_pct,
        public ?string $fuente,
        public int     $tamano_pt,
        public string  $color,
        public string  $alineacion,
        public bool    $negrita,
        public bool    $cursiva,
        public string  $mayusculas,
        public ?string $valor_fijo,
        public bool    $activo,
        public int     $orden,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id:           (int)   $row->id,
            plantilla_id: (int)   $row->plantilla_id,
            clave:                $row->clave,
            etiqueta:             $row->etiqueta ?? $row->clave,
            tipo:                 $row->tipo ?? 'texto',
            pos_x_pct:    (float) ($row->pos_x_pct ?? 50),
            pos_y_pct:    (float) ($row->pos_y_pct ?? 50),
            ancho_pct:    isset($row->ancho_pct) ? (float) $row->ancho_pct : null,
            alto_pct:     isset($row->alto_pct)  ? (float) $row->alto_pct  : null,
            fuente:               $row->fuente ?? null,
            tamano_pt:    (int)   ($row->tamano_pt ?? 36),
            color:                $row->color ?? '#000000',
            alineacion:           $row->alineacion ?? 'center',
            negrita:      (bool)  ($row->negrita ?? false),
            cursiva:      (bool)  ($row->cursiva  ?? false),
            mayusculas:           $row->mayusculas ?? 'none',
            valor_fijo:           $row->valor_fijo ?? null,
            activo:       (bool)  ($row->activo ?? true),
            orden:        (int)   ($row->orden ?? 0),
        );
    }
}
