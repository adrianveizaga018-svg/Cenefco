<?php

namespace App\Application\Articulos\Commands;

final readonly class UpdateArticuloCommand
{
    public function __construct(
        public int     $id,
        public ?string $titulo,
        public ?string $slug,
        public ?string $entradilla,
        public ?string $contenido,
        public ?string $imagen_principal_url,
        public ?string $imagen_alt,
        public ?bool   $destacada,
        public ?string $fecha_publicacion,
        public ?string $estado_web,
        public ?string $meta_titulo,
        public ?string $meta_descripcion,
        public ?int    $estado,
        public ?array  $etiquetas,
    ) {}
}
