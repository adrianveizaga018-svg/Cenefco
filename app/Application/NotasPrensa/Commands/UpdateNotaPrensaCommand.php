<?php

declare(strict_types=1);

namespace App\Application\NotasPrensa\Commands;

final readonly class UpdateNotaPrensaCommand
{
    public function __construct(
        public int|string $id,
        public ?string $titulo = null,
        public ?string $medio = null,
        public ?string $logo_medio_url = null,
        public ?string $logo_medio_alt = null,
        public ?string $resumen = null,
        public ?string $url_noticia = null,
        public ?string $fecha_publicacion = null,
        public ?bool $destacada = null,
        public ?int $orden = null,
        public ?bool $activo = null,
    ) {}
}
