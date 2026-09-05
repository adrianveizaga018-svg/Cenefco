<?php

namespace App\Application\Eventos\DTOs;

final readonly class EventoDTO
{
    public function __construct(
        public int     $id,
        public string  $titulo,
        public string  $slug,
        public ?string $entradilla,
        public ?string $descripcion,
        public ?string $imagen_url,
        public ?string $imagen_alt,
        public ?string $tipo,
        public ?string $modalidad,
        public ?string $lugar,
        public ?string $url_transmision,
        public ?string $url_registro,
        public bool    $gratuito,
        public ?string $precio,
        public ?int    $cupo_maximo,
        public ?int    $programa_id,
        public ?string $fecha_inicio,
        public ?string $fecha_fin,
        public bool    $todo_el_dia,
        public bool    $destacado,
        public int     $vistas,
        public ?string $meta_titulo,
        public ?string $meta_descripcion,
        public string  $estado,
        public ?string $created_at,
    ) {}

    public static function fromModel(object $m): self
    {
        return new self(
            id:               $m->id,
            titulo:           $m->titulo,
            slug:             $m->slug,
            entradilla:       $m->entradilla ?? null,
            descripcion:      $m->descripcion ?? null,
            imagen_url:       $m->imagen_url ?? null,
            imagen_alt:       $m->imagen_alt ?? null,
            tipo:             $m->tipo ?? null,
            modalidad:        $m->modalidad ?? null,
            lugar:            $m->lugar ?? null,
            url_transmision:  $m->url_transmision ?? null,
            url_registro:     $m->url_registro ?? null,
            gratuito:         (bool) ($m->gratuito ?? true),
            precio:           $m->precio ?? null,
            cupo_maximo:      $m->cupo_maximo ? (int) $m->cupo_maximo : null,
            programa_id:      $m->programa_id ? (int) $m->programa_id : null,
            fecha_inicio:     $m->fecha_inicio?->toIso8601String(),
            fecha_fin:        $m->fecha_fin?->toIso8601String(),
            todo_el_dia:      (bool) ($m->todo_el_dia ?? false),
            destacado:        (bool) ($m->destacado ?? false),
            vistas:           (int) ($m->vistas ?? 0),
            meta_titulo:      $m->meta_titulo ?? null,
            meta_descripcion: $m->meta_descripcion ?? null,
            estado:           $m->estado ?? 'programado',
            created_at:       $m->created_at?->toIso8601String(),
        );
    }
}
