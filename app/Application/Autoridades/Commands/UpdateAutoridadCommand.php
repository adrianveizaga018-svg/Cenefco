<?php

namespace App\Application\Autoridades\Commands;

final readonly class UpdateAutoridadCommand
{
    public function __construct(
        public int     $id,
        public ?string $nombre              = null,
        public ?string $apellido            = null,
        public ?string $cargo               = null,
        public ?string $tipo                = null,
        public ?int    $secretaria_id       = null,
        public ?string $perfil_profesional  = null,
        public ?string $email_institucional = null,
        public ?string $foto_url            = null,
        public ?int    $orden               = null,
        public ?bool   $activo              = null,
        public ?bool   $publicado_web       = null,
        public ?string $fecha_inicio_cargo  = null,
        public ?string $fecha_fin_cargo     = null,
    ) {}
}
