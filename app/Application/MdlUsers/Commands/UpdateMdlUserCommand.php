<?php

namespace App\Application\MdlUsers\Commands;

final readonly class UpdateMdlUserCommand
{
    public function __construct(
        public int $id,
        public ?string $nombreUsuario,
        public ?string $nombre,
        public ?string $appaterno,
        public ?string $apmaterno,
        public ?string $ci,
        public ?int $expedido,
        public ?string $telefono,
        public ?string $celular,
        public ?string $email,
        public ?string $direccion,
        public ?string $ciudad,
        public ?int $estado,
        public ?int $perModificar,
    ) {}
}
