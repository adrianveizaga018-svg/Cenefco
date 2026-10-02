<?php

namespace App\Application\CampanasPublicidad\DTOs;

final readonly class ReporteCampanaDTO
{
    public function __construct(
        public ?int    $programa_id,
        public string  $programa_nombre,
        public float   $total_invertido,
        public int     $total_alcance,
        public int     $total_resultados,
        public ?float  $costo_por_resultado_promedio,
        public ?int    $total_inscritos_curso,
        public ?float  $total_recaudado_curso,
        public ?float  $retorno_aproximado,
    ) {}

    public static function fromRow(object $r): self
    {
        $totalInvertido = (float) ($r->total_invertido ?? 0);
        $totalResultados = (int) ($r->total_resultados ?? 0);
        $totalRecaudado = isset($r->total_recaudado_curso) ? (float) $r->total_recaudado_curso : null;

        return new self(
            programa_id:                    $r->programa_id ? (int) $r->programa_id : null,
            programa_nombre:                 $r->programa_nombre ?? 'Institucional / Otro',
            total_invertido:                 $totalInvertido,
            total_alcance:                   (int) ($r->total_alcance ?? 0),
            total_resultados:                $totalResultados,
            costo_por_resultado_promedio:     $totalResultados > 0 ? round($totalInvertido / $totalResultados, 2) : null,
            total_inscritos_curso:            isset($r->total_inscritos_curso) ? (int) $r->total_inscritos_curso : null,
            total_recaudado_curso:            $totalRecaudado,
            retorno_aproximado:               ($totalRecaudado !== null && $totalInvertido > 0) ? round($totalRecaudado / $totalInvertido, 2) : null,
        );
    }
}
