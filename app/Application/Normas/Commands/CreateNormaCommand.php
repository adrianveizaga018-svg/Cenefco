<?php

namespace App\Application\Normas\Commands;

final readonly class CreateNormaCommand
{
    public function __construct(
        public int $tipo_norma_id,
        public string $numero,
        public string $titulo,
        public ?string $resumen = null,
        public ?string $texto_completo = null,
        public ?string $archivo_pdf_url = null,
        public ?string $fecha_promulgacion = null,
        public ?string $fecha_publicacion_gaceta = null,
        public string $estado_vigencia = 'vigente',
        public ?string $tema_principal = null,
        public ?string $palabras_clave = null,
        public ?int $publicado_por = null,
    ) {}
}
