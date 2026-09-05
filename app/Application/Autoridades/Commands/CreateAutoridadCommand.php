<?php

namespace App\Application\Autoridades\Commands;

final readonly class CreateAutoridadCommand
{
    public function __construct(
        public string $nombre,
        public string $apellido,
        public string $cargo,
        public string $tipo = 'director',
        public ?int $secretaria_id = null,
        public ?string $perfil_profesional = null,
        public ?string $email_institucional = null,
        public ?string $foto_url = null,
        public int $orden = 0,
        public bool $activo = true,
        public bool $publicado_web = false,
        public ?string $fecha_inicio_cargo = null,
        public ?string $fecha_fin_cargo = null,
    ) {}
}
