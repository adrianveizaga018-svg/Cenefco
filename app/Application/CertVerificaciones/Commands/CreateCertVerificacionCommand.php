<?php

namespace App\Application\CertVerificaciones\Commands;

final readonly class CreateCertVerificacionCommand
{
    public function __construct(
        public ?int    $certificado_id,
        public string  $codigo_consultado,
        public string  $resultado,
        public ?string $ip_origen,
        public ?string $user_agent,
        public ?string $pais,
    ) {}
}
