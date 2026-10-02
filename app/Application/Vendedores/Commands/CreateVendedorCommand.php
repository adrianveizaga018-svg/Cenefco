<?php

namespace App\Application\Vendedores\Commands;

final readonly class CreateVendedorCommand
{
    public function __construct(
        public string  $nombre,
        public string  $apellido,
        public ?string $ci           = null,
        public ?string $telefono     = null,
        public ?string $email        = null,
        public ?string $foto         = null,
        public ?string $pagina       = null,
        public ?float  $meta_ventas  = null,
        public bool    $activo       = true,
        public ?int    $usuario_id   = null,
    ) {}
}
