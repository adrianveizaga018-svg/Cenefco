<?php

namespace App\Application\Testimonios\DTOs;

final readonly class TestimonioDTO
{
    public function __construct(
        public int     $id,
        public string  $nombre,
        public ?string $slug,
        public ?string $cargo,
        public ?string $empresa,
        public string  $testimonio,
        public int     $calificacion,
        public ?string $foto_url,
        public ?string $foto_alt,
        public ?int    $programa_id,
        public bool    $destacado,
        public int     $orden,
        public string  $estado,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}

    public static function fromModel(object $m): self
    {
        return new self(
            id:           $m->id,
            nombre:       $m->nombre,
            slug:         $m->slug ?? null,
            cargo:        $m->cargo ?? null,
            empresa:      $m->empresa ?? null,
            testimonio:   $m->testimonio,
            calificacion: (int) ($m->calificacion ?? 5),
            foto_url:     $m->foto_url ?? null,
            foto_alt:     $m->foto_alt ?? null,
            programa_id:  $m->programa_id ? (int) $m->programa_id : null,
            destacado:    (bool) ($m->destacado ?? false),
            orden:        (int) ($m->orden ?? 0),
            estado:       $m->estado ?? 'publicado',
            created_at:   $m->created_at?->toIso8601String(),
            updated_at:   $m->updated_at?->toIso8601String(),
        );
    }
}
