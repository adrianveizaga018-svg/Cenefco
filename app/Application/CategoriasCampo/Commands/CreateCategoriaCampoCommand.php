<?php

namespace App\Application\CategoriasCampo\Commands;

final readonly class CreateCategoriaCampoCommand
{
    public function __construct(
        public int $categoria_id,
        public string $nombre_campo,
        public string $etiqueta,
        public string $tipo_campo = 'text',
        public bool $requerido = false,
        public int $orden = 0,
        public int $paso = 2,
        public bool $activo = true,
        public ?string $ayuda = null,
        public ?array $opciones = null,
        public ?array $validacion = null,
    ) {}
}
