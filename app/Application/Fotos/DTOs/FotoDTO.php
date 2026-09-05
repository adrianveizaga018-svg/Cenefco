<?php

namespace App\Application\Fotos\DTOs;

final readonly class FotoDTO
{
    public function __construct(
        public int     $id_foto,
        public int     $id_us_reg,
        public int     $num_foto,
        public string  $titulo_foto,
        public ?string $descripcion_foto,
        public ?string $foto,
        public ?string $fecha_foto,
        public int     $estado,
        public ?string $fecha_reg,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id_foto:          $row->id_foto,
            id_us_reg:        (int) ($row->id_us_reg ?? 0),
            num_foto:         (int) ($row->num_foto ?? 0),
            titulo_foto:      $row->titulo_foto,
            descripcion_foto: $row->descripcion_foto ?? null,
            foto:             $row->foto ?? null,
            fecha_foto:       $row->fecha_foto ?? null,
            estado:           (int) ($row->estado ?? 1),
            fecha_reg:        $row->fecha_reg ?? null,
        );
    }
}
