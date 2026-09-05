<?php

namespace App\Application\DocentesPerfil\Commands;

final readonly class UpdateDocentePerfilCommand
{
    public function __construct(
        public int     $id,
        public ?int    $usuario_id       = null,
        public ?string $nombre_completo  = null,
        public ?string $titulo_academico = null,
        public ?string $especialidad     = null,
        public ?string $biografia        = null,
        public ?string $foto_url         = null,
        public ?string $foto_alt         = null,
        public ?string $email_publico    = null,
        public ?string $telefono         = null,
        public ?string $linkedin_url     = null,
        public ?string $twitter_url      = null,
        public ?string $sitio_web_url    = null,
        public ?string $tipo             = null,
        public ?bool   $mostrar_en_web   = null,
        public ?int    $orden            = null,
        public ?string $estado           = null,
    ) {}
}
