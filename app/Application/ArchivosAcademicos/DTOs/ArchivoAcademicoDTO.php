<?php

namespace App\Application\ArchivosAcademicos\DTOs;

final readonly class ArchivoAcademicoDTO
{
    public function __construct(
        public int $idArch,
        public ?string $nombre,
        public ?string $ruta,
        public ?string $tipo,
        public int $estado,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            idArch: $row->id_arch,
            nombre: $row->nombre ?? null,
            ruta: $row->ruta ?? null,
            tipo: $row->tipo ?? null,
            estado: $row->estado,
        );
    }
}
