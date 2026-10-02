<?php

namespace App\Application\DirectorioArchivos\DTOs;

final readonly class ParticipanteDirectorioDTO
{
    public function __construct(
        public int $id_ins,
        public int $id_us,
        public string $nombre_completo,
        public ?string $ci,
        public ?string $email,
        public ?string $fecha_ins,
        public int $total_archivos,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id_ins: (int) $row->id_ins,
            id_us: (int) $row->id_us,
            nombre_completo: (string) $row->nombre_completo,
            ci: $row->ci,
            email: $row->email,
            fecha_ins: $row->fecha_ins,
            total_archivos: (int) $row->total_archivos,
        );
    }
}
