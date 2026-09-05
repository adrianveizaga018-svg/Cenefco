<?php

namespace App\Application\MdlCourses\DTOs;

final readonly class MdlCourseDTO
{
    public function __construct(
        public int $id,
        public ?int $idUsReg,
        public ?int $numModcurso,
        public ?string $fullname,
        public ?string $shortname,
        public ?int $idDocente,
        public ?int $category,
        public ?string $sigla,
        public ?string $paralelo,
        public ?string $cupo,
        public ?string $gradoAcademico1,
        public ?string $gradoAcademico2,
        public ?string $observacionImp,
        public ?string $imparteFechaInicio,
        public ?string $imparteFechaFin,
        public ?string $imparteFechaActa,
        public ?int $ocultarCoordinadorActa,
        public ?int $idCoordinador,
        public ?string $gradoCoordinador1,
        public ?string $gradoCoordinador2,
        public ?string $tituloPersonalizado,
        public ?string $subtituloPersonalizado,
        public ?string $gestion,
        public ?int $perModificar,
        public ?string $fechaReg,
        public int $estado,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id:                      $row->id,
            idUsReg:                 $row->id_us_reg ?? null,
            numModcurso:             $row->num_modcurso ?? null,
            fullname:                $row->fullname ?? null,
            shortname:               $row->shortname ?? null,
            idDocente:               $row->id_docente ?? null,
            category:                $row->category ?? null,
            sigla:                   $row->sigla ?? null,
            paralelo:                $row->paralelo ?? null,
            cupo:                    $row->cupo ?? null,
            gradoAcademico1:         $row->grado_academico1 ?? null,
            gradoAcademico2:         $row->grado_academico2 ?? null,
            observacionImp:          $row->observacion_imp ?? null,
            imparteFechaInicio:      isset($row->imparte_fecha_inicio) ? (string) $row->imparte_fecha_inicio : null,
            imparteFechaFin:         isset($row->imparte_fecha_fin) ? (string) $row->imparte_fecha_fin : null,
            imparteFechaActa:        isset($row->imparte_fecha_acta) ? (string) $row->imparte_fecha_acta : null,
            ocultarCoordinadorActa:  $row->ocultar_coordinador_acta ?? null,
            idCoordinador:           $row->id_coordinador ?? null,
            gradoCoordinador1:       $row->grado_coordinador1 ?? null,
            gradoCoordinador2:       $row->grado_coordinador2 ?? null,
            tituloPersonalizado:     $row->titulo_personalizado ?? null,
            subtituloPersonalizado:  $row->subtitulo_personalizado ?? null,
            gestion:                 $row->gestion ?? null,
            perModificar:            $row->per_modificar ?? null,
            fechaReg:                isset($row->fecha_reg) ? (string) $row->fecha_reg : null,
            estado:                  (int) $row->estado,
        );
    }
}
