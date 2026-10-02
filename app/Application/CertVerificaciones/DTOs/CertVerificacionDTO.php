<?php

namespace App\Application\CertVerificaciones\DTOs;

final readonly class CertVerificacionDTO
{
    public function __construct(
        public int     $id,
        public ?int    $certificado_id,
        public string  $codigo_consultado,
        public string  $resultado,
        public ?string $ip_origen,
        public ?string $user_agent,
        public ?string $pais,
        public ?string $created_at,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id:                (int)   $row->id,
            certificado_id:    isset($row->certificado_id) ? (int) $row->certificado_id : null,
            codigo_consultado:         $row->codigo_consultado,
            resultado:                 $row->resultado,
            ip_origen:                 $row->ip_origen  ?? null,
            user_agent:                $row->user_agent ?? null,
            pais:                      $row->pais       ?? null,
            created_at:        isset($row->created_at) ? (string) $row->created_at : null,
        );
    }
}
