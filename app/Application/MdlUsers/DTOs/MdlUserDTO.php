<?php

namespace App\Application\MdlUsers\DTOs;

final readonly class MdlUserDTO
{
    public function __construct(
        public int $id,
        public ?int $idUsReg,
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
        public ?string $fechaReg,
        public int $estado,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id:            $row->id,
            idUsReg:       $row->id_us_reg ?? null,
            numModusuario: $row->num_modusuario ?? null,
            nombreUsuario: $row->nombre_usuario ?? null,
            nombre:        $row->nombre ?? null,
            appaterno:     $row->appaterno ?? null,
            apmaterno:     $row->apmaterno ?? null,
            ci:            $row->ci ?? null,
            expedido:      $row->expedido ?? null,
            telefono:      $row->telefono ?? null,
            celular:       $row->celular ?? null,
            email:         $row->email ?? null,
            direccion:     $row->direccion ?? null,
            ciudad:        $row->ciudad ?? null,
            perModificar:  $row->per_modificar ?? null,
            fechaReg:      isset($row->fecha_reg) ? (string) $row->fecha_reg : null,
            estado:        (int) $row->estado,
        );
    }
}
