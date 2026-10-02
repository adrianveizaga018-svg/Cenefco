<?php

declare(strict_types=1);

namespace App\Application\NotasPrensa\Commands;

final readonly class CreateNotaPrensaCommand
{
    public function __construct(
        public string $titulo,
        public string $medio,
        public string $fecha_publicacion,
        public bool $destacada = false,
        public int $orden = 0,
        public bool $activo = true,
        public ?string $logo_medio_url = null,
        public ?string $logo_medio_alt = null,
        public ?string $resumen = null,
        public ?string $url_noticia = null,
    ) {}
}
