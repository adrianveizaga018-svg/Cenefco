<?php

namespace App\Application\CertConfigProgramas\DTOs;

final readonly class CertConfigProgramaDTO
{
    public function __construct(
        public int $id,
        public int $programa_id,
        public bool $activo,
        public ?string $titulo,
        public ?string $descripcion,
        public ?string $created_at,
        public ?string $updated_at,
        public array $items = [],
        public ?string $nombre_programa = null,
    ) {}

    public static function fromRow(object $row, array $items = [], ?string $nombrePrograma = null): self
    {
        return new self(
            id:          (int) $row->id,
            programa_id: (int) $row->programa_id,
            activo:      (bool) $row->activo,
            titulo:      $row->titulo ?? null,
            descripcion: $row->descripcion ?? null,
            created_at:  isset($row->created_at) ? (string) $row->created_at : null,
            updated_at:  isset($row->updated_at) ? (string) $row->updated_at : null,
            items:       $items,
            nombre_programa: $nombrePrograma ?? ($row->nombre_programa ?? null),
        );
    }
}
