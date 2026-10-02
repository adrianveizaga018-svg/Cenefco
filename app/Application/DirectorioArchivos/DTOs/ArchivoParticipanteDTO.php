<?php

namespace App\Application\DirectorioArchivos\DTOs;

final readonly class ArchivoParticipanteDTO
{
    public function __construct(
        public int $id_ins,
        public int $id_us,
        public string $nombre_completo,
        public ?string $ci,
        public ?string $curso_nombre,
        public array $archivos,
    ) {}
}
