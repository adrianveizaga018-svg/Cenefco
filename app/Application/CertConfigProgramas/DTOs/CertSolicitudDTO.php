<?php

namespace App\Application\CertConfigProgramas\DTOs;

final readonly class CertSolicitudDTO
{
    public function __construct(
        public int $id,
        public int $config_item_id,
        public ?int $inscripcion_id,
        public string $usuario_ci,
        public string $usuario_nombre,
        public ?string $usuario_email,
        public bool $es_gratuito,
        public string $estado,
        public ?string $comprobante_url,
        public ?float $monto_pagado,
        public ?string $nota_admin,
        public ?int $certificado_id,
        public ?string $created_at,
        public ?string $updated_at,
        public ?string $nombre_cert = null,
        public ?string $nombre_programa = null,
        public ?string $certificado_url = null,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id:              (int) $row->id,
            config_item_id:  (int) $row->config_item_id,
            inscripcion_id:  isset($row->inscripcion_id) ? (int) $row->inscripcion_id : null,
            usuario_ci:      $row->usuario_ci,
            usuario_nombre:  $row->usuario_nombre,
            usuario_email:   $row->usuario_email ?? null,
            es_gratuito:     (bool) $row->es_gratuito,
            estado:          $row->estado,
            comprobante_url: $row->comprobante_url ?? null,
            monto_pagado:    isset($row->monto_pagado) ? (float) $row->monto_pagado : null,
            nota_admin:      $row->nota_admin ?? null,
            certificado_id:  isset($row->certificado_id) ? (int) $row->certificado_id : null,
            created_at:      isset($row->created_at) ? (string) $row->created_at : null,
            updated_at:      isset($row->updated_at) ? (string) $row->updated_at : null,
            nombre_cert:     $row->nombre_cert ?? null,
            nombre_programa: $row->nombre_programa ?? null,
            certificado_url: $row->certificado_url ?? null,
        );
    }
}
