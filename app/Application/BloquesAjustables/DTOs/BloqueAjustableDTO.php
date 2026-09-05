<?php

namespace App\Application\BloquesAjustables\DTOs;

final readonly class BloqueAjustableDTO
{
    public function __construct(
        public int $idBloqueajustable,
        public ?int $idPagina,
        public ?int $idBloqueplantilla,
        public ?string $titulo,
        public int $estado,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            idBloqueajustable: $row->id_bloqueajustable,
            idPagina: $row->id_pagina ?? null,
            idBloqueplantilla: $row->id_bloqueplantilla ?? null,
            titulo: $row->titulo ?? null,
            estado: $row->estado,
        );
    }
}
