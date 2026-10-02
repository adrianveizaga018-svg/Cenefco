<?php

namespace App\Application\DirectorioArchivos\DTOs;

final readonly class CursoDirectorioDTO
{
    public function __construct(
        public int $id_imp,
        public string $nombre,
        public ?string $docente,
        public ?string $periodo,
        public ?string $gestion,
        public int $participantes_con_archivos,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id_imp: (int) $row->id_imp,
            nombre: (string) $row->nombre,
            docente: $row->docente !== '' ? $row->docente : null,
            periodo: $row->periodo,
            gestion: $row->gestion,
            participantes_con_archivos: (int) $row->participantes_con_archivos,
        );
    }
}
