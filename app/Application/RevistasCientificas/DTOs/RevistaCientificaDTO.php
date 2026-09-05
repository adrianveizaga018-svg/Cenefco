<?php

namespace App\Application\RevistasCientificas\DTOs;

final readonly class RevistaCientificaDTO
{
    public function __construct(
        public int     $id_revistacientifica,
        public int     $id_us_reg,
        public int     $num_revistacientifica,
        public string  $titulo_revistacientifica,
        public ?string $descripcion_revistacientifica,
        public ?string $fecha_publicacion,
        public ?string $archivo,
        public int     $estado,
        public ?string $slug,
        public ?string $fecha_reg,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id_revistacientifica:          $row->id_revistacientifica,
            id_us_reg:                     (int) ($row->id_us_reg ?? 0),
            num_revistacientifica:         (int) ($row->num_revistacientifica ?? 0),
            titulo_revistacientifica:      $row->titulo_revistacientifica,
            descripcion_revistacientifica: $row->descripcion_revistacientifica ?? null,
            fecha_publicacion:             $row->fecha_publicacion ?? null,
            archivo:                       $row->archivo ?? null,
            estado:                        (int) ($row->estado ?? 1),
            slug:                          $row->slug ?? null,
            fecha_reg:                     $row->fecha_reg ?? null,
        );
    }
}
