<?php

namespace App\Application\Vendedores\Commands;

final readonly class UpdateVendedorCommand
{
    public function __construct(
        public int     $id,
        public ?string $nombre      = null,
        public ?string $apellido    = null,
        public ?string $ci          = null,
        public ?string $telefono    = null,
        public ?string $email       = null,
        public ?string $foto        = null,
        public ?string $pagina      = null,
        public ?float  $meta_ventas = null,
        public ?bool   $activo      = null,
        public ?int    $usuario_id  = null,
        public array   $provided    = [],
    ) {}

    public function toArray(): array
    {
        $todos = [
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'ci' => $this->ci,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'foto' => $this->foto,
            'pagina' => $this->pagina,
            'meta_ventas' => $this->meta_ventas,
            'activo' => $this->activo,
            'usuario_id' => $this->usuario_id,
        ];

        return array_intersect_key($todos, array_flip($this->provided));
    }
}
