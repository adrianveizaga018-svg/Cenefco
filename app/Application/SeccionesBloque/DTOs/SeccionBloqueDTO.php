<?php

namespace App\Application\SeccionesBloque\DTOs;

final readonly class SeccionBloqueDTO
{
    public function __construct(
        public int $idSeccionbloque,
        public ?int $idBloqueajustable,
        public ?string $titulo,
        public ?string $contenido,
        public int $estado,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            idSeccionbloque: $row->id_seccionbloque,
            idBloqueajustable: $row->id_bloqueajustable ?? null,
            titulo: $row->titulo ?? null,
            contenido: $row->contenido ?? null,
            estado: $row->estado,
        );
    }
}
