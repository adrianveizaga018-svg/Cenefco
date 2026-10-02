<?php

namespace App\Application\DocentesPerfil\Commands;

final readonly class CreateDocentePerfilCommand
{
    public function __construct(
        public string  $nombre_completo,
        public ?int    $usuario_id       = null,
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
        public string  $tipo             = 'docente',
        public bool    $mostrar_en_web   = true,
        public int     $orden            = 0,
        public string  $estado           = 'publicado',
    ) {}
}
