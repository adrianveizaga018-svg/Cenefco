<?php

namespace App\Application\Articulos\DTOs;

final readonly class ArticuloDTO
{
    public function __construct(
        public int    $id_art,
        public string $titulo,
        public string $slug,
        public ?string $entradilla,
        public ?string $contenido,
        public ?string $imagen_principal_url,
        public ?string $imagen_alt,
        public bool   $destacada,
        public ?string $fecha_publicacion,
        public string $estado_web,
        public int    $estado,
        public int    $vistas,
        public ?string $meta_titulo,
        public ?string $meta_descripcion,
        public int    $id_us_reg,
        public int    $num_art,
        public ?string $deleted_at,
        public ?string $fecha_reg,
        public ?string $updated_at,
        public array  $etiquetas,
    ) {}

    public static function fromRow(object $row): self
    {
        $etiquetas = isset($row->etiquetas) && $row->etiquetas
            ? json_decode($row->etiquetas, false) ?? []
            : [];

        return new self(
            id_art:               $row->id_art,
            titulo:               $row->titulo,
            slug:                 $row->slug,
            entradilla:           $row->entradilla ?? null,
            contenido:            $row->contenido ?? null,
            imagen_principal_url: $row->imagen_principal_url ?? null,
            imagen_alt:           $row->imagen_alt ?? null,
            destacada:            (bool) ($row->destacada ?? false),
            fecha_publicacion:    $row->fecha_publicacion ?? null,
            estado_web:           $row->estado_web ?? 'borrador',
            estado:               (int) ($row->estado ?? 1),
            vistas:               (int) ($row->vistas ?? 0),
            meta_titulo:          $row->meta_titulo ?? null,
            meta_descripcion:     $row->meta_descripcion ?? null,
            id_us_reg:            (int) ($row->id_us_reg ?? 0),
            num_art:              (int) ($row->num_art ?? 0),
            deleted_at:           $row->deleted_at ?? null,
            fecha_reg:            $row->fecha_reg ?? null,
            updated_at:           $row->updated_at ?? null,
            etiquetas:            $etiquetas,
        );
    }
}
