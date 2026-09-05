<?php

namespace App\Application\SpeechVentas\DTOs;

final readonly class SpeechVentasDTO
{
    public function __construct(
        public int $id,
        public string $titulo,
        public ?string $categoria,
        public string $contenido,
        public ?string $palabrasClave,
        public bool $activo,
        public int $orden,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id:           (int) $row->id,
            titulo:       $row->titulo,
            categoria:    $row->categoria ?? null,
            contenido:    $row->contenido,
            palabrasClave:$row->palabras_clave ?? null,
            activo:       (bool) $row->activo,
            orden:        (int) ($row->orden ?? 0),
            createdAt:    isset($row->created_at) ? (string) $row->created_at : null,
            updatedAt:    isset($row->updated_at) ? (string) $row->updated_at : null,
        );
    }
}
