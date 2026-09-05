<?php
namespace App\Application\Popups\Commands;
final readonly class UpdatePopupCommand {
    public function __construct(
        public int $id, public ?string $titulo = null, public ?string $contenido = null,
        public ?string $imagen_url = null, public ?string $enlace_url = null,
        public ?string $enlace_texto = null, public ?string $posicion = null,
        public ?int $delay_segundos = null, public ?bool $mostrar_una_vez_sesion = null,
        public ?bool $mostrar_una_vez_siempre = null, public ?string $paginas_mostrar = null,
        public ?bool $activo = null, public ?string $fecha_inicio = null, public ?string $fecha_fin = null,
    ) {}
}
