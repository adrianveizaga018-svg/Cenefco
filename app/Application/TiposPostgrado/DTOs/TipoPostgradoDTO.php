<?php

namespace App\Application\TiposPostgrado\DTOs;

final readonly class TipoPostgradoDTO
{
    public function __construct(
        public int $idTipopost,
        public ?int $idPlan,
        public ?int $idUsReg,
        public ?int $numTipopost,
        public ?int $idTipopago,
        public ?string $descuentopostgrado,
        public ?string $calculoCuota,
        public ?string $fechaReg,
        public int $estado,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            idTipopost:         (int) $row->id_tipopost,
            idPlan:             isset($row->id_plan) ? (int) $row->id_plan : null,
            idUsReg:            isset($row->id_us_reg) ? (int) $row->id_us_reg : null,
            numTipopost:        isset($row->num_tipopost) ? (int) $row->num_tipopost : null,
            idTipopago:         isset($row->id_tipopago) ? (int) $row->id_tipopago : null,
            descuentopostgrado: $row->descuentopostgrado ?? null,
            calculoCuota:       $row->calculo_cuota ?? null,
            fechaReg:           isset($row->fecha_reg) ? (string) $row->fecha_reg : null,
            estado:             (int) $row->estado,
        );
    }
}
