<?php

namespace App\Application\Tesis\DTOs;

final readonly class TesisDTO
{
    public function __construct(
        public int     $id_tesis,
        public int     $id_us_reg,
        public int     $num_tesis,
        public string  $titulo_tesis,
        public ?string $descripcion_tesis,
        public ?string $fecha_publicacion,
        public ?string $autor,
        public ?int    $tipo_tesis,
        public ?string $archivo,
        public int     $estado,
        public ?string $slug,
        public ?string $fecha_reg,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id_tesis:          $row->id_tesis,
            id_us_reg:         (int) ($row->id_us_reg ?? 0),
            num_tesis:         (int) ($row->num_tesis ?? 0),
            titulo_tesis:      $row->titulo_tesis,
            descripcion_tesis: $row->descripcion_tesis ?? null,
            fecha_publicacion: $row->fecha_publicacion ?? null,
            autor:             $row->autor ?? null,
            tipo_tesis:        isset($row->tipo_tesis) ? (int) $row->tipo_tesis : null,
            archivo:           $row->archivo ?? null,
            estado:            (int) ($row->estado ?? 1),
            slug:              $row->slug ?? null,
            fecha_reg:         $row->fecha_reg ?? null,
        );
    }
}
