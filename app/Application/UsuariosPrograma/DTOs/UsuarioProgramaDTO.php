<?php

namespace App\Application\UsuariosPrograma\DTOs;

final readonly class UsuarioProgramaDTO
{
    public function __construct(
        public int $id_usuarioprograma,
        public int $id_us,
        public int $id_us_reg,
        public int $num_usuarioprograma,
        public ?int $id_programa,
        public ?int $id_tipoprograma,
        public int $estado,
        public ?string $fecha_reg,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id_usuarioprograma:  (int) $row->id_usuarioprograma,
            id_us:               (int) $row->id_us,
            id_us_reg:           (int) $row->id_us_reg,
            num_usuarioprograma: (int) $row->num_usuarioprograma,
            id_programa:         isset($row->id_programa) ? (int) $row->id_programa : null,
            id_tipoprograma:     isset($row->id_tipoprograma) ? (int) $row->id_tipoprograma : null,
            estado:              (int) $row->estado,
            fecha_reg:           $row->fecha_reg ?? null,
        );
    }
}
