<?php

namespace App\Application\Normas\DTOs;

final readonly class NormaDTO
{
    public function __construct(
        public int $id,
        public int $tipo_norma_id,
        public string $tipo_nombre,
        public string $numero,
        public string $titulo,
        public ?string $resumen,
        public ?string $archivo_pdf_url,
        public ?string $fecha_promulgacion,
        public ?string $fecha_publicacion_gaceta,
        public string $estado_vigencia,
        public ?string $tema_principal,
        public ?string $palabras_clave,
        public int $vistas,
        public string $slug,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id: (int) $model->id,
            tipo_norma_id: (int) $model->tipo_norma_id,
            tipo_nombre: $model->tipo?->nombre ?? '',
            numero: $model->numero,
            titulo: $model->titulo,
            resumen: $model->resumen,
            archivo_pdf_url: $model->archivo_pdf_url,
            fecha_promulgacion: $model->fecha_promulgacion?->toDateString(),
            fecha_publicacion_gaceta: $model->fecha_publicacion_gaceta?->toDateString(),
            estado_vigencia: $model->estado_vigencia,
            tema_principal: $model->tema_principal,
            palabras_clave: $model->palabras_clave,
            vistas: (int) $model->vistas,
            slug: $model->slug,
        );
    }
}
