<?php

namespace App\Application\EventosFotos\DTOs;

final readonly class EventoFotoDTO
{
    public function __construct(
        public int $id,
        public int $evento_id,
        public string $archivo_url,
        public ?string $titulo,
        public int $orden,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id: $model->id,
            evento_id: (int) $model->evento_id,
            archivo_url: $model->archivo_url
                ? (str_starts_with($model->archivo_url, '/storage/') || str_starts_with($model->archivo_url, 'http')
                    ? $model->archivo_url
                    : '/storage/'.$model->archivo_url)
                : '',
            titulo: $model->titulo,
            orden: (int) $model->orden,
        );
    }
}
