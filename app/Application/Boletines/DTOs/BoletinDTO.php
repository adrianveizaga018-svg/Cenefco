<?php

namespace App\Application\Boletines\DTOs;

final readonly class BoletinDTO
{
    public function __construct(
        public int     $id_boletin,
        public int     $id_us_reg,
        public int     $num_boletin,
        public ?string $titulo_pagina,
        public string  $titulo_boletin,
        public ?string $descripcion_boletin,
        public int     $estado,
        public ?string $imagen_url,
        public ?string $slug,
        public ?string $fecha_reg,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id_boletin:          $row->id_boletin,
            id_us_reg:           (int) ($row->id_us_reg ?? 0),
            num_boletin:         (int) ($row->num_boletin ?? 0),
            titulo_pagina:       $row->titulo_pagina ?? null,
            titulo_boletin:      $row->titulo_boletin,
            descripcion_boletin: $row->descripcion_boletin ?? null,
            estado:              (int) ($row->estado ?? 1),
            imagen_url:          $row->imagen_url ?? null,
            slug:                $row->slug ?? null,
            fecha_reg:           $row->fecha_reg ?? null,
        );
    }
}
