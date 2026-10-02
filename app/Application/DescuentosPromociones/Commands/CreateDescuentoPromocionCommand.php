<?php

namespace App\Application\DescuentosPromociones\Commands;

final readonly class CreateDescuentoPromocionCommand
{
    public function __construct(
        public string  $codigo,
        public string  $nombre,
        public float   $valor,
        public string  $tipo_descuento   = 'porcentaje',
        public ?string $descripcion      = null,
        public ?float  $monto_minimo     = null,
        public ?int    $usos_maximos     = null,
        public int     $usos_por_usuario = 1,
        public ?int    $programa_id      = null,
        public bool    $activo           = true,
        public ?string $fecha_inicio     = null,
        public ?string $fecha_fin        = null,
    ) {}
}
