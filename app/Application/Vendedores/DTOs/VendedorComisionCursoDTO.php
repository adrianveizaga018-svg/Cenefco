<?php

namespace App\Application\Vendedores\DTOs;

final readonly class VendedorComisionCursoDTO
{
    public function __construct(
        public int     $id_programa,
        public string  $nombre_programa,
        public int     $total_inscritos,
        public ?string $categoria_nombre,
        public float   $comision_monto,
        public float   $comision_estimada,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id_programa:       (int) $row->id_programa,
            nombre_programa:   $row->nombre_programa ?? '(Sin nombre)',
            total_inscritos:   (int) $row->total_inscritos,
            categoria_nombre:  $row->categoria_nombre ?? null,
            comision_monto:    (float) $row->comision_monto,
            comision_estimada: (float) $row->comision_estimada,
        );
    }
}
