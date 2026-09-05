<?php

namespace App\Application\Acreditaciones\Commands;

final readonly class CreateAcreditacionCommand
{
    public function __construct(
        public string  $nombre,
        public string  $entidad_otorgante,
        public ?string $tipo,
        public ?string $descripcion,
        public ?string $logo_url,
        public ?string $logo_alt,
        public ?string $url_verificacion,
        public ?string $fecha_obtencion,
        public ?string $fecha_vencimiento,
        public int     $orden,
        public bool    $activo,
    ) {}
}
