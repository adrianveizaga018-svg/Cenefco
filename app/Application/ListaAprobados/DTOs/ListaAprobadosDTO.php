<?php

namespace App\Application\ListaAprobados\DTOs;

final readonly class ListaAprobadosDTO
{
    public function __construct(
        public int     $id,
        public int     $imparte_id,
        public int     $usuario_id,
        public ?int    $inscripcion_id,
        public ?float  $nota_final,
        public ?float  $nota_minima,
        public string  $condicion,
        public ?string $observacion,
        public ?string $comprobante_url,
        public bool    $ajuste_manual,
        public string  $estado_certificado,
        public bool    $notificado_email,
        public ?int    $registrado_por,
        public int     $id_us_reg,
        public ?string $created_at,
        public ?string $updated_at,
        public ?string $curso_nombre,
        public ?string $curso_periodo,
        public ?string $curso_gestion,
        public ?string $estudiante_nombre,
        public ?string $estudiante_appaterno,
        public ?string $estudiante_apmaterno,
        public ?string $estudiante_nombre_pila,
        public ?string $estudiante_ci,
        public ?string $estudiante_email,
        public ?string $cert_archivo_url,
        public ?string $cert_codigo,
        public ?int    $cert_id,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id:                  (int)  $row->id,
            imparte_id:          (int)  $row->imparte_id,
            usuario_id:          (int)  $row->usuario_id,
            inscripcion_id:      isset($row->inscripcion_id) ? (int)  $row->inscripcion_id  : null,
            nota_final:          isset($row->nota_final)     ? (float) $row->nota_final     : null,
            nota_minima:         isset($row->nota_minima)    ? (float) $row->nota_minima    : null,
            condicion:                 $row->condicion ?? 'aprobado',
            observacion:               $row->observacion ?? null,
            comprobante_url:           $row->comprobante_url ?? null,
            ajuste_manual:       (bool) ($row->ajuste_manual ?? false),
            estado_certificado:        $row->estado_certificado ?? 'pendiente',
            notificado_email:    (bool) ($row->notificado_email ?? false),
            registrado_por:      isset($row->registrado_por) ? (int)  $row->registrado_por  : null,
            id_us_reg:           (int)  ($row->id_us_reg ?? 0),
            created_at:          isset($row->created_at) ? (string) $row->created_at : null,
            updated_at:          isset($row->updated_at) ? (string) $row->updated_at : null,
            curso_nombre:              $row->curso_nombre ?? null,
            curso_periodo:             $row->curso_periodo ?? null,
            curso_gestion:             $row->curso_gestion ?? null,
            estudiante_nombre:         $row->estudiante_nombre ?? null,
            estudiante_appaterno:      $row->estudiante_appaterno ?? null,
            estudiante_apmaterno:      $row->estudiante_apmaterno ?? null,
            estudiante_nombre_pila:    $row->estudiante_nombre_pila ?? null,
            estudiante_ci:             $row->estudiante_ci ?? null,
            estudiante_email:          $row->estudiante_email ?? null,
            cert_archivo_url:          $row->cert_archivo_url ?? null,
            cert_codigo:               $row->cert_codigo ?? null,
            cert_id:             isset($row->cert_id) ? (int) $row->cert_id : null,
        );
    }
}
