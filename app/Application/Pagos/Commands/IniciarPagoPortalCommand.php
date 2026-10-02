<?php

namespace App\Application\Pagos\Commands;

final readonly class IniciarPagoPortalCommand
{
    public function __construct(
        public int     $idPrograma,
        public string  $nombre,
        public string  $appaterno,
        public string  $ci,
        public ?string $expedido,
        public string  $email,
        public ?string $telefono,
        public ?int    $idFechapago,
        public float   $monto,
        public string  $successUrl,
        public string  $cancelUrl,
    ) {}
}
