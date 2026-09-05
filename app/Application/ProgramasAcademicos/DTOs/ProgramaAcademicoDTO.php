<?php

namespace App\Application\ProgramasAcademicos\DTOs;

final readonly class ProgramaAcademicoDTO
{
    public function __construct(
        public int $id_programa,
        public ?int $id_us_reg,
        public ?int $num_programa,
        public string $nombre_programa,
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
        public int $estado,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id_programa:              (int) $model->id_programa,
            id_us_reg:                isset($model->id_us_reg) ? (int) $model->id_us_reg : null,
            num_programa:             isset($model->num_programa) ? (int) $model->num_programa : null,
            nombre_programa:          (string) $model->nombre_programa,
            descripcion:              $model->descripcion ?? null,
            foto:                     $model->foto ?? null,
            inicio_actividades:       $model->inicio_actividades ?? null,
            finalizacion_actividades: $model->finalizacion_actividades ?? null,
            inicio_inscripciones:     $model->inicio_inscripciones ?? null,
            titulo_documento1:        $model->titulo_documento1 ?? null,
            documento1:               $model->documento1 ?? null,
            titulo_documento2:        $model->titulo_documento2 ?? null,
            documento2:               $model->documento2 ?? null,
            titulo_documento3:        $model->titulo_documento3 ?? null,
            documento3:               $model->documento3 ?? null,
            titulo_documento4:        $model->titulo_documento4 ?? null,
            documento4:               $model->documento4 ?? null,
            dirigido:                 $model->dirigido ?? null,
            inversion:                $model->inversion ?? null,
            requisitos:               $model->requisitos ?? null,
            creditaje:                $model->creditaje ?? null,
            objetivo:                 $model->objetivo ?? null,
            nota:                     $model->nota ?? null,
            id_tipoprograma:          isset($model->id_tipoprograma) ? (int) $model->id_tipoprograma : null,
            url_video:                $model->url_video ?? null,
            estado:                   (int) $model->estado,
        );
    }
}
