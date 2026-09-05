<?php

declare(strict_types=1);

namespace App\Application\Descargables\Commands;

final readonly class CreateDescargableCommand
{
    public function __construct(
        public string $nombre,
        public string $archivo_url,
        public bool $requiere_datos = true,
        public int $orden = 0,
        public bool $activo = true,
        public ?string $tipo = null,
        public ?string $imagen_portada_url = null,
        public ?int $programa_id = null,
    ) {}
}
