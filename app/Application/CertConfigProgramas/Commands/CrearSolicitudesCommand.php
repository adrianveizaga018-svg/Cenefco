<?php

namespace App\Application\CertConfigProgramas\Commands;

final readonly class CrearSolicitudesCommand
{
    public function __construct(
        public int $programa_id,
        public string $usuario_ci,
        public string $usuario_nombre,
        public ?string $usuario_email,
        public ?int $inscripcion_id,
        public array $items_pago_ids,
        public bool $pagar_ahora,
        public ?string $comprobante_url,
    ) {}
}
