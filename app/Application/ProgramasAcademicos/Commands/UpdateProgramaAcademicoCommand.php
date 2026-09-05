<?php

namespace App\Application\ProgramasAcademicos\Commands;

final readonly class UpdateProgramaAcademicoCommand
{
    public function __construct(
        public int $id,
        public ?string $nombre_programa,
        public ?string $descripcion,
        public ?string $foto,
        public ?string $inicio_actividades,
        public ?string $finalizacion_actividades,
        public ?string $inicio_inscripciones,
        public ?string $titulo_documento1,
        public ?string $documento1,
        public ?string $titulo_documento2,
        public ?string $documento2,
        public ?string $titulo_documento3,
        public ?string $documento3,
        public ?string $titulo_documento4,
        public ?string $documento4,
        public ?string $dirigido,
        public ?string $inversion,
        public ?string $requisitos,
        public ?string $creditaje,
        public ?string $objetivo,
        public ?string $nota,
        public ?int $id_tipoprograma,
        public ?string $url_video,
        public ?int $estado,
    ) {}
}
