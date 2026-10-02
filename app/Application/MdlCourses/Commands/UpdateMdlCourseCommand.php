<?php

namespace App\Application\MdlCourses\Commands;

final readonly class UpdateMdlCourseCommand
{
    public function __construct(
        public int $id,
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
        public ?int $estado,
        public ?int $perModificar,
    ) {}
}
