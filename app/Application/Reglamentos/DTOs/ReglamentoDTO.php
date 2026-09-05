<?php

namespace App\Application\Reglamentos\DTOs;

final readonly class ReglamentoDTO
{
    private const DEFAULTS = [
        'bienvenida'         => null,
        'reglas_asistencia'  => "La asistencia mínima requerida es del 80% de las sesiones programadas.\nEl ingreso tardío (más de 10 min.) será registrado como media asistencia.\nEn caso de ausencia justificada, comunicar al coordinador con anticipación.\nTres inasistencias consecutivas sin justificación podrán derivar en la pérdida de la matrícula.",
        'reglas_evaluacion'  => "Las evaluaciones y trabajos deben entregarse en las fechas indicadas.\nNo se aceptan trabajos plagiados; la deshonestidad académica puede causar la pérdida del programa.\nLa nota mínima de aprobación es 60/100.\nEn caso de reprobación, el estudiante podrá solicitar una evaluación de segunda instancia.",
        'reglas_pagos'       => "Los pagos deben realizarse en las fechas establecidas en el plan de pagos.\nEl atraso en cuotas podrá limitar el acceso a materiales y evaluaciones.\nNo se emitirá certificado hasta que el plan de pagos esté completamente cancelado.\nPara consultas sobre pagos, comunicarse con Secretaría Académica.",
        'reglas_conducta'    => "Se exige un trato respetuoso hacia docentes, personal y compañeros.\nQueda prohibido el uso del celular durante las sesiones, salvo autorización expresa.\nNo se permite el ingreso de personas ajenas al programa sin autorización previa.\nToda conducta inapropiada será evaluada por el Comité Académico.",
        'reglas_plataformas' => "Conectarse con nombre completo real en las sesiones virtuales por Zoom.\nLos grupos de WhatsApp son de uso estrictamente académico.\nNo se permite compartir los enlaces de Zoom o grupos con personas externas.\nLas grabaciones de las clases son de uso exclusivo de los participantes inscritos.",
        'reglas_derechos'    => "Recibir formación de calidad con docentes certificados.\nAcceso a materiales y recursos del programa.\nRecibir su certificado al completar el programa y los pagos correspondientes.\nSolicitar atención personalizada en Secretaría Académica.\nPresentar sugerencias y/o quejas ante la Dirección Académica.",
    ];

    public function __construct(
        public int     $id_programa,
        public bool    $personalizado,
        public ?string $bienvenida,
        public bool    $bienvenida_personalizado,
        public ?string $reglas_asistencia,
        public bool    $reglas_asistencia_personalizado,
        public ?string $reglas_evaluacion,
        public bool    $reglas_evaluacion_personalizado,
        public ?string $reglas_pagos,
        public bool    $reglas_pagos_personalizado,
        public ?string $reglas_conducta,
        public bool    $reglas_conducta_personalizado,
        public ?string $reglas_plataformas,
        public bool    $reglas_plataformas_personalizado,
        public ?string $reglas_derechos,
        public bool    $reglas_derechos_personalizado,
    ) {}

    public static function fromRow(int $idPrograma, ?object $row): self
    {
        $fields = array_keys(self::DEFAULTS);
        $args   = ['id_programa' => $idPrograma, 'personalizado' => (bool) $row];

        foreach ($fields as $field) {
            $value          = $row ? ($row->$field ?? null) : null;
            $args[$field]   = $value ?: self::DEFAULTS[$field];
            $args["{$field}_personalizado"] = $row && $row->$field !== null;
        }

        return new self(...$args);
    }
}
