<?php

namespace App\Application\UsuariosPlanDoc\DTOs;

final readonly class UsuarioPlanDocDTO
{
    public function __construct(
        public int $id_usuarioplandoc,
        public int $id_us,
        public int $id_us_reg,
        public int $num_usuarioplandoc,
        public ?int $id_plandoc,
        public int $estado,
        public ?string $fecha_reg,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id_usuarioplandoc:  (int) $row->id_usuarioplandoc,
            id_us:              (int) $row->id_us,
            id_us_reg:          (int) $row->id_us_reg,
            num_usuarioplandoc: (int) $row->num_usuarioplandoc,
            id_plandoc:         isset($row->id_plandoc) ? (int) $row->id_plandoc : null,
            estado:             (int) $row->estado,
            fecha_reg:          $row->fecha_reg ?? null,
        );
    }
}
