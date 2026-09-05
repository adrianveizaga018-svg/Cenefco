<?php
namespace App\Application\GaleriasVideos\DTOs;
final readonly class GaleriaVideoDTO {
    public function __construct(
        public int $id,
        public string $titulo,
        public ?string $descripcion,
        public string $plataforma,
        public string $url_video,
        public ?string $video_id,
        public ?string $miniatura_url,
        public ?string $duracion,
        public ?string $tipo,
        public ?int $programa_id,
        public bool $destacado,
        public int $orden,
        public int $vistas,
        public bool $activo,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}
    public static function fromModel(object $m): self {
        return new self(
            id: $m->id,
            titulo: $m->titulo,
            descripcion: $m->descripcion ?? null,
            plataforma: $m->plataforma ?? 'youtube',
            url_video: $m->url_video,
            video_id: $m->video_id ?? null,
            miniatura_url: $m->miniatura_url ?? null,
            duracion: $m->duracion ?? null,
            tipo: $m->tipo ?? null,
            programa_id: isset($m->programa_id) ? (int)$m->programa_id : null,
            destacado: (bool)($m->destacado ?? false),
            orden: (int)($m->orden ?? 0),
            vistas: (int)($m->vistas ?? 0),
            activo: (bool)($m->activo ?? false),
            created_at: $m->created_at?->toIso8601String(),
            updated_at: $m->updated_at?->toIso8601String(),
        );
    }
}
