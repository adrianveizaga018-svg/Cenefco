<?php
namespace App\Application\Popups\DTOs;
final readonly class PopupDTO {
    public function __construct(
        public int $id, public ?string $titulo, public ?string $contenido,
        public ?string $imagen_url, public ?string $enlace_url, public ?string $enlace_texto,
        public string $posicion, public int $delay_segundos,
        public bool $mostrar_una_vez_sesion, public bool $mostrar_una_vez_siempre,
        public ?string $paginas_mostrar, public bool $activo,
        public ?string $fecha_inicio, public ?string $fecha_fin,
        public ?string $created_at, public ?string $updated_at,
    ) {}
    public static function fromModel(object $m): self {
        return new self(
            id: $m->id, titulo: $m->titulo ?? null, contenido: $m->contenido ?? null,
            imagen_url: $m->imagen_url ?? null, enlace_url: $m->enlace_url ?? null,
            enlace_texto: $m->enlace_texto ?? null, posicion: $m->posicion ?? 'center',
            delay_segundos: (int)($m->delay_segundos ?? 3),
            mostrar_una_vez_sesion: (bool)($m->mostrar_una_vez_sesion ?? true),
            mostrar_una_vez_siempre: (bool)($m->mostrar_una_vez_siempre ?? false),
            paginas_mostrar: $m->paginas_mostrar ?? null, activo: (bool)($m->activo ?? false),
            fecha_inicio: $m->fecha_inicio?->toIso8601String(), fecha_fin: $m->fecha_fin?->toIso8601String(),
            created_at: $m->created_at?->toIso8601String(), updated_at: $m->updated_at?->toIso8601String(),
        );
    }
}
