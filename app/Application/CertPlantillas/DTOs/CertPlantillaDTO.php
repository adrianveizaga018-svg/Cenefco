<?php

namespace App\Application\CertPlantillas\DTOs;

final readonly class CertPlantillaDTO
{
    public function __construct(
        public int $id,
        public string $nombre,
        public string $tipo,
        public string $imagen_url,
        public int $ancho_px,
        public int $alto_px,
        public string $orientacion,
        public string $formato_salida,
        public int $calidad_jpg,
        public ?string $fuente_default,
        public ?string $color_default,
        public string $estado,
        public ?string $notas,
        public int $id_us_reg,
        public ?string $created_at,
        public ?string $updated_at,
        public array $campos = [],
    ) {}

    public static function fromRow(object $row, array $campos = []): self
    {
        return new self(
            id:             (int) $row->id,
            nombre:         $row->nombre,
            tipo:           $row->tipo ?? 'aprobacion',
            imagen_url:     $row->imagen_url,
            ancho_px:       (int) ($row->ancho_px ?? 3508),
            alto_px:        (int) ($row->alto_px  ?? 2480),
            orientacion:    $row->orientacion ?? 'horizontal',
            formato_salida: $row->formato_salida ?? 'jpg',
            calidad_jpg:    (int) ($row->calidad_jpg ?? 95),
            fuente_default: $row->fuente_default ?? null,
            color_default:  $row->color_default  ?? null,
            estado:         $row->estado ?? 'activo',
            notas:          $row->notas  ?? null,
            id_us_reg:      (int) ($row->id_us_reg ?? 0),
            created_at:     isset($row->created_at) ? (string) $row->created_at : null,
            updated_at:     isset($row->updated_at) ? (string) $row->updated_at : null,
            campos:         $campos,
        );
    }
}
