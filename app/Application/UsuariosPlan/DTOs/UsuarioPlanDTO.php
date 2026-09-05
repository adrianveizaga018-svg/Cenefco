<?php

namespace App\Application\UsuariosPlan\DTOs;

final readonly class UsuarioPlanDTO
{
    public function __construct(
        public int $id_usuarioplan,
        public int $id_us,
        public int $id_us_reg,
        public int $num_usuarioplan,
        public ?int $id_plan,
        public int $estado,
        public ?string $fecha_reg,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id_usuarioplan: (int) $row->id_usuarioplan,
            id_us:          (int) $row->id_us,
            id_us_reg:      (int) $row->id_us_reg,
            num_usuarioplan:(int) $row->num_usuarioplan,
            id_plan:        isset($row->id_plan) ? (int) $row->id_plan : null,
            estado:         (int) $row->estado,
            fecha_reg:      $row->fecha_reg ?? null,
        );
    }
}
