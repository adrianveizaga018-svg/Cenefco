<?php

namespace App\Application\DocumentosTransparencia\DTOs;

final readonly class DocumentoDTO
{
    private const TIPO_CATEGORIA_MAP = [
        1 => 'declaracion_bienes',
        2 => 'contrato',
        3 => 'informe',
        4 => 'informe',
        5 => 'presupuesto',
        6 => 'presupuesto',
        7 => 'informe',
        8 => 'ordenanza',
        9 => 'plan_anual',
        10 => 'resolucion',
        11 => 'informe',
        12 => 'rendicion_cuentas',
    ];

    public function __construct(
        public int $id,
        public int $tipo_documento_id,
        public ?int $secretaria_id,
        public ?int $publicado_por,
        public string $titulo,
        public ?string $descripcion,
        public ?string $archivo_url,
        public int $gestion,
        public ?string $fecha_publicacion,
        public bool $activo,
        public string $slug,
        public ?string $created_at,
        public ?string $tipo_nombre = null,
        public ?string $secretaria_nombre = null,
        public string $categoria = 'informe',
        public int $anio = 0,
        public bool $publicado = false,
    ) {}

    public static function fromModel(object $model): self
    {
        $tipoId = (int) $model->tipo_documento_id;

        return new self(
            id: $model->id,
            tipo_documento_id: $tipoId,
            secretaria_id: $model->secretaria_id ? (int) $model->secretaria_id : null,
            publicado_por: $model->publicado_por ? (int) $model->publicado_por : null,
            titulo: $model->titulo,
            descripcion: $model->descripcion,
            archivo_url: $model->archivo_url,
            gestion: (int) $model->gestion,
            fecha_publicacion: $model->fecha_publicacion?->toDateString(),
            activo: (bool) $model->activo,
            slug: $model->slug,
            created_at: $model->created_at ? (is_string($model->created_at) ? $model->created_at : $model->created_at->toIso8601String()) : null,
            tipo_nombre: $model->tipoDocumento?->nombre,
            secretaria_nombre: $model->secretaria?->nombre,
            categoria: self::TIPO_CATEGORIA_MAP[$tipoId] ?? 'informe',
            anio: (int) $model->gestion,
            publicado: (bool) $model->activo,
        );
    }
}
