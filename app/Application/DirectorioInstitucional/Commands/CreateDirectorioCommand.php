<?php

namespace App\Application\DirectorioInstitucional\Commands;

final readonly class CreateDirectorioCommand
{
    public function __construct(
        public ?int $secretaria_id,
        public string $nombre,
        public ?string $descripcion,
        public ?string $responsable,
        public ?string $cargo_responsable,
        public ?string $telefono,
        public ?string $telefono_interno,
        public ?string $email,
        public ?string $foto_url,
        public ?string $ubicacion,
        public ?string $horario,
        public int $orden,
        public bool $activo,
    ) {}
}
