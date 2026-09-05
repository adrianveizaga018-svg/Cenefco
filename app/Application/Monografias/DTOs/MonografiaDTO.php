<?php

namespace App\Application\Monografias\DTOs;

final readonly class MonografiaDTO
{
    public function __construct(
        public int     $id_monografia,
        public int     $id_us_reg,
        public int     $num_monografia,
        public string  $titulo_monografia,
        public ?string $descripcion_monografia,
        public ?string $fecha_publicacion,
        public ?string $autor,
        public ?string $archivo,
        public int     $estado,
        public ?string $slug,
        public ?string $fecha_reg,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id_monografia:          $row->id_monografia,
            id_us_reg:              (int) ($row->id_us_reg ?? 0),
            num_monografia:         (int) ($row->num_monografia ?? 0),
            titulo_monografia:      $row->titulo_monografia,
            descripcion_monografia: $row->descripcion_monografia ?? null,
            fecha_publicacion:      $row->fecha_publicacion ?? null,
            autor:                  $row->autor ?? null,
            archivo:                $row->archivo ?? null,
            estado:                 (int) ($row->estado ?? 1),
            slug:                   $row->slug ?? null,
            fecha_reg:              $row->fecha_reg ?? null,
        );
    }
}
