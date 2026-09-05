<?php

namespace App\Application\MdlUsers\Commands;

final readonly class CreateMdlUserCommand
{
    public function __construct(
        public int $id,
        public ?int $numModusuario,
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
        public ?int $perModificar,
        public int $idUsReg,
    ) {}
}
