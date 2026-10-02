<?php

namespace App\Application\Certificados\DTOs;

final readonly class CertificadoDTO
{
    public function __construct(
        public int     $id,
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
        public ?string $motivo_anulacion,
        public ?int    $anulado_por,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id:                       (int)   $row->id,
            lista_aprobado_id:        (int)   $row->lista_aprobado_id,
            plantilla_id:             (int)   $row->plantilla_id,
            usuario_id:               (int)   $row->usuario_id,
            imparte_id:               (int)   $row->imparte_id,
            nombre_en_certificado:            $row->nombre_en_certificado,
            programa_en_certificado:          $row->programa_en_certificado,
            condicion:                        $row->condicion,
            nota_final:               isset($row->nota_final)    ? (float) $row->nota_final    : null,
            horas_academicas:         isset($row->horas_academicas) ? (int) $row->horas_academicas : null,
            fecha_inicio_curso:       isset($row->fecha_inicio_curso) ? (string) $row->fecha_inicio_curso : null,
            fecha_fin_curso:          isset($row->fecha_fin_curso)  ? (string) $row->fecha_fin_curso  : null,
            codigo_verificacion:              $row->codigo_verificacion,
            qr_url:                           $row->qr_url ?? null,
            archivo_url:                      $row->archivo_url ?? null,
            archivo_miniatura_url:            $row->archivo_miniatura_url ?? null,
            estado:                           $row->estado ?? 'generado',
            motivo_anulacion:                 $row->motivo_anulacion ?? null,
            anulado_por:              isset($row->anulado_por) ? (int) $row->anulado_por : null,
            created_at:               isset($row->created_at) ? (string) $row->created_at : null,
            updated_at:               isset($row->updated_at) ? (string) $row->updated_at : null,
        );
    }
}
