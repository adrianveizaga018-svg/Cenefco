<?php

namespace App\Application\Cursos\Commands;

final readonly class CreateCursoCommand
{
    public function __construct(
        public string  $nombre_programa,
        public ?string $slug                     = null,
        public ?string $descripcion              = null,
        public ?string $objetivo                 = null,
        public ?string $dirigido                 = null,
        public ?string $requisitos               = null,
        public ?string $inversion                = null,
        public ?float  $costo_monto              = null,
        public ?string $creditaje                = null,
        public ?string $nota                     = null,
        public ?string $foto                     = null,
        public ?string $titulo_documento1        = null,
        public ?string $documento1               = null,
        public ?string $imagen_banner_url        = null,
        public ?string $imagen_alt               = null,
        public ?string $url_video                = null,
        public ?string $url_whatsapp             = null,
        public ?string $url_whatsapp2            = null,
        public ?array  $imagenes                 = null,
        public ?string $inicio_actividades       = null,
        public ?string $finalizacion_actividades = null,
        public ?string $inicio_inscripciones     = null,
        public ?string $tipo_honorario            = null,
        public ?int    $id_tipoprograma          = null,
        public ?int    $categoria_web_id         = null,
        public ?int    $formulario_id               = null,
        public ?int    $area_id                  = null,
        public string  $estado_web               = 'borrador',
        public bool    $destacado                = false,
        public int     $orden                    = 0,
        public ?string $meta_titulo              = null,
        public ?string $meta_descripcion         = null,
        public ?string $mensaje_exito            = null,
        public ?int    $id_plan                  = null,
        public ?int    $id_plandoc               = null,
        public ?int    $id_imp                   = null,
        public ?int    $convenio_id              = null,
        public ?int    $vendedor_id              = null,
        public ?array  $tareas_catalogo_ids      = null,
        public ?array  $planes                   = null,
    ) {}
}
