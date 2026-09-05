<?php

namespace App\Application\TiposPrograma\DTOs;

final readonly class TipoProgramaDTO
{
    public function __construct(
        public int $idTipoprograma,
        public ?int $idUsReg,
        public ?int $numTipoprograma,
        public string $nombreTipoprograma,
        public ?int $perModificar,
        public ?string $fechaReg,
        public int $estado,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            idTipoprograma:     (int) $row->id_tipoprograma,
            idUsReg:            isset($row->id_us_reg) ? (int) $row->id_us_reg : null,
            numTipoprograma:    isset($row->num_tipoprograma) ? (int) $row->num_tipoprograma : null,
            nombreTipoprograma: $row->nombre_tipoprograma,
            perModificar:       isset($row->per_modificar) ? (int) $row->per_modificar : null,
            fechaReg:           isset($row->fecha_reg) ? (string) $row->fecha_reg : null,
            estado:             (int) $row->estado,
        );
    }
}
