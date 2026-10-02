<?php

namespace App\Application\CertConfigProgramas\DTOs;

final readonly class CertConfigItemDTO
{
    public function __construct(
        public int $id,
        public int $config_id,
        public ?int $plantilla_id,
        public string $nombre_cert,
        public float $precio,
        public bool $es_gratuito,
        public int $orden,
        public bool $activo,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id:           (int) $row->id,
            config_id:    (int) $row->config_id,
            plantilla_id: isset($row->plantilla_id) ? (int) $row->plantilla_id : null,
            nombre_cert:  $row->nombre_cert,
            precio:       (float) ($row->precio ?? 0),
            es_gratuito:  (bool) $row->es_gratuito,
            orden:        (int) ($row->orden ?? 0),
            activo:       (bool) $row->activo,
        );
    }
}
