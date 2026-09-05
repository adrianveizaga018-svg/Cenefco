<?php

namespace App\Application\Noticias\DTOs;

final readonly class NoticiaDTO
{
    public function __construct(
        public int $id,
        public int $categoria_id,
        public int $autor_id,
        public string $titulo,
        public string $slug,
        public ?string $entradilla,
        public ?string $cuerpo,
        public ?string $imagen_principal_url,
        public ?string $imagen_alt,
        public string $estado,
        public bool $destacada,
        public ?string $fecha_publicacion,
        public int $vistas,
        public ?string $meta_titulo,
        public ?string $meta_descripcion,
        public ?string $created_at,
        public array $etiquetas = [],
        public ?array $categoria = null,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id: $model->id,
            categoria_id: $model->categoria_id,
            autor_id: $model->autor_id,
            titulo: $model->titulo,
            slug: $model->slug,
            entradilla: $model->entradilla,
            cuerpo: $model->cuerpo,
            imagen_principal_url: $model->imagen_principal_url
                ? (str_starts_with($model->imagen_principal_url, '/storage/') || str_starts_with($model->imagen_principal_url, 'http')
                    ? $model->imagen_principal_url
                    : '/storage/'.$model->imagen_principal_url)
                : null,
            imagen_alt: $model->imagen_alt,
            estado: $model->estado,
            destacada: (bool) $model->destacada,
            fecha_publicacion: $model->fecha_publicacion?->toIso8601String(),
            vistas: (int) $model->vistas,
            meta_titulo: $model->meta_titulo,
            meta_descripcion: $model->meta_descripcion,
            created_at: (is_string($model->created_at ?? null) ? $model->created_at : $model->created_at?->toIso8601String()),
            etiquetas: $model->relationLoaded('etiquetas')
                                    ? $model->etiquetas->map(fn ($e) => ['id' => $e->id, 'nombre' => $e->nombre])->toArray()
                                    : [],
            categoria: $model->relationLoaded('categoria') && $model->categoria
                                    ? ['id' => $model->categoria->id, 'nombre' => $model->categoria->nombre]
                                    : null,
        );
    }
}
