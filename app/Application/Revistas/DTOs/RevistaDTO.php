<?php

namespace App\Application\Revistas\DTOs;

final readonly class RevistaDTO
{
    public function __construct(
        public int     $id_revista,
        public int     $id_us_reg,
        public int     $num_revista,
        public string  $titulo_revista,
        public ?string $descripcion_revista,
        public ?string $fecha_publicacion,
        public ?string $archivo,
        public int     $estado,
        public ?string $slug,
        public ?string $fecha_reg,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id_revista:          $row->id_revista,
            id_us_reg:           (int) ($row->id_us_reg ?? 0),
            num_revista:         (int) ($row->num_revista ?? 0),
            titulo_revista:      $row->titulo_revista,
            descripcion_revista: $row->descripcion_revista ?? null,
            fecha_publicacion:   $row->fecha_publicacion ?? null,
            archivo:             $row->archivo ?? null,
            estado:              (int) ($row->estado ?? 1),
            slug:                $row->slug ?? null,
            fecha_reg:           $row->fecha_reg ?? null,
        );
    }
}
