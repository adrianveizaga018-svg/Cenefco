<?php

namespace App\Application\Certificados\Commands;

final readonly class CreateCertificadoCommand
{
    public function __construct(
        public int     $lista_aprobado_id,
        public int     $plantilla_id,
        public int     $usuario_id,
        public int     $imparte_id,
        public string  $nombre_en_certificado,
        public string  $programa_en_certificado,
        public string  $condicion,
        public ?float  $nota_final,
        public ?int    $horas_academicas,
        public ?string $fecha_inicio_curso,
        public ?string $fecha_fin_curso,
        public string  $codigo_verificacion,
        public ?string $qr_url,
        public ?string $archivo_url,
        public ?string $archivo_miniatura_url,
        public string  $estado,
    ) {}
}
