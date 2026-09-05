<?php

namespace App\Application\Honorarios\DTOs;

final readonly class DocenteHonorarioSugeridoDTO
{
    public function __construct(
        public int     $id_us,
        public string  $docente_nombre,
        public int     $id_imp,
        public string  $nombre_curso,
        public int     $id_programa,
        public string  $nombre_programa,
        public string  $tipo_honorario,
        public int     $dias_dictados,
        public float   $monto_sugerido,
        public bool    $ya_registrado,
    ) {}
}
