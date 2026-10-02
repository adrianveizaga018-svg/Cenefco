<?php

namespace App\Application\UsuariosTipoPrograma\DTOs;

final readonly class UsuarioTipoProgramaDTO
{
    public function __construct(
        public int $id_usuariotipoprograma,
        public int $id_us,
        public int $id_us_reg,
        public int $num_usuariotipoprograma,
        public ?int $id_tipoprograma,
        public int $estado,
        public ?string $fecha_reg,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id_usuariotipoprograma:  (int) $row->id_usuariotipoprograma,
            id_us:                   (int) $row->id_us,
            id_us_reg:               (int) $row->id_us_reg,
            num_usuariotipoprograma: (int) $row->num_usuariotipoprograma,
            id_tipoprograma:         isset($row->id_tipoprograma) ? (int) $row->id_tipoprograma : null,
            estado:                  (int) $row->estado,
            fecha_reg:               $row->fecha_reg ?? null,
        );
    }
}
