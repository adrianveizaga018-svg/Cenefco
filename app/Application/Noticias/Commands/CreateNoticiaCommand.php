<?php

namespace App\Application\Noticias\Commands;

final readonly class CreateNoticiaCommand
{
    public function __construct(
        public int $categoria_id,
        public int $autor_id,
        public string $titulo,
        public ?string $entradilla = null,
        public ?string $cuerpo = null,
        public ?string $imagen_principal_url = null,
        public ?string $imagen_alt = null,
        public string $estado = 'borrador',
        public bool $destacada = false,
        public ?string $fecha_publicacion = null,
        public ?string $meta_titulo = null,
        public ?string $meta_descripcion = null,
        public array $etiquetas = [],
    ) {}
}
