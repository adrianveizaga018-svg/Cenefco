<?php

namespace App\Application\Universidades\DTOs;

final readonly class UniversidadDTO
{
    public function __construct(
        public int     $id_universidad,
        public string  $nombre_universidad,
        public ?int    $id_ciudad,
        public ?int    $id_tipouniversidad,
        public int     $estado,
        public int     $id_us_reg,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id_universidad:      $row->id_universidad,
            nombre_universidad:  $row->nombre_universidad,
            id_ciudad:           isset($row->id_ciudad) ? (int) $row->id_ciudad : null,
            id_tipouniversidad:  isset($row->id_tipouniversidad) ? (int) $row->id_tipouniversidad : null,
            estado:              (int) ($row->estado ?? 1),
            id_us_reg:           (int) ($row->id_us_reg ?? 0),
        );
    }
}
