<?php
namespace App\Application\Eventos\Commands;
final readonly class CreateEventoCommand {
    public function __construct(
        public string  $titulo,
        public ?string $entradilla     = null,
        public ?string $descripcion    = null,
        public ?string $imagen_url     = null,
        public ?string $imagen_alt     = null,
        public ?string $tipo           = null,
        public ?string $modalidad      = null,
        public ?string $lugar          = null,
        public ?string $url_transmision= null,
        public ?string $url_registro   = null,
        public bool    $gratuito       = true,
        public ?string $precio         = null,
        public ?int    $cupo_maximo    = null,
        public ?int    $programa_id    = null,
        public ?string $fecha_inicio   = null,
        public ?string $fecha_fin      = null,
        public bool    $todo_el_dia    = false,
        public bool    $destacado      = false,
        public ?string $meta_titulo    = null,
        public ?string $meta_descripcion = null,
        public string  $estado         = 'programado',
    ) {}
}
