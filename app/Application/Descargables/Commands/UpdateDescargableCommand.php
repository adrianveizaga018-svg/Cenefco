<?php

declare(strict_types=1);

namespace App\Application\Descargables\Commands;

final readonly class UpdateDescargableCommand
{
    public function __construct(
        public int|string $id,
        public ?string $nombre = null,
        public ?string $tipo = null,
        public ?string $archivo_url = null,
        public ?string $imagen_portada_url = null,
        public ?int $programa_id = null,
        public ?bool $requiere_datos = null,
        public ?int $orden = null,
        public ?bool $activo = null,
    ) {}
}
