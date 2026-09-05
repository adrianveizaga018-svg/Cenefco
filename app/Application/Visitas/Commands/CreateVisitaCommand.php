<?php

namespace App\Application\Visitas\Commands;

final readonly class CreateVisitaCommand
{
    public function __construct(
        public string  $session_id,
        public string  $url,
        public string  $ruta,
        public ?string $titulo,
        public ?string $referrer,
        public ?string $pais,
        public ?string $ciudad,
        public ?string $dispositivo,
        public ?string $navegador,
        public ?string $so,
        public ?int    $duracion_seg,
    ) {}
}
