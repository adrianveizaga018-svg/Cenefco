<?php

namespace App\Application\DescuentosPromociones\Commands;

final readonly class UpdateDescuentoPromocionCommand
{
    public function __construct(
        public int     $id,
        public ?string $codigo           = null,
        public ?string $nombre           = null,
        public ?string $descripcion      = null,
        public ?string $tipo_descuento   = null,
        public ?float  $valor            = null,
        public ?float  $monto_minimo     = null,
        public ?int    $usos_maximos     = null,
        public ?int    $usos_por_usuario = null,
        public ?int    $programa_id      = null,
        public ?bool   $activo           = null,
        public ?string $fecha_inicio     = null,
        public ?string $fecha_fin        = null,
    ) {}
}
