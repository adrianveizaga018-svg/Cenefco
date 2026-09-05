<?php

namespace App\Application\CategoriasCampo\Commands;

final readonly class UpdateCategoriaCampoCommand
{
    public function __construct(
        public int $id,
        public ?string $etiqueta = null,
        public ?string $tipo_campo = null,
        public ?bool $requerido = null,
        public ?int $orden = null,
        public ?int $paso = null,
        public ?bool $activo = null,
        public ?string $ayuda = null,
        public ?array $opciones = null,
        public ?array $validacion = null,
    ) {}
}
