<?php

namespace App\Application\UsuariosAcademicos\Commands;

final readonly class UpdateUsuarioAcademicoCommand
{
    public function __construct(
        public int $id,
        public ?string $tipoestudiante,
        public ?string $nombre_usuario,
        public ?string $categoria,
        public ?string $titulo_academico,
        public ?string $appaterno,
        public ?string $apmaterno,
        public ?string $nombre,
        public ?string $ci,
        public ?int $expedido,
        public ?string $telefono,
        public ?string $celular,
        public ?int $genero,
        public ?string $email,
        public ?string $direccion,
        public ?string $ciudad,
        public ?string $pais,
        public ?int $id_universidad,
        public ?int $id_carrera,
        public ?int $estado,
    ) {}
}
