<?php
namespace App\Application\Popups\Commands;
final readonly class CreatePopupCommand {
    public function __construct(
        public ?string $titulo = null, public ?string $contenido = null,
        public ?string $imagen_url = null, public ?string $enlace_url = null,
        public ?string $enlace_texto = null, public string $posicion = 'center',
        public int $delay_segundos = 3, public bool $mostrar_una_vez_sesion = true,
        public bool $mostrar_una_vez_siempre = false, public ?string $paginas_mostrar = null,
        public bool $activo = false, public ?string $fecha_inicio = null, public ?string $fecha_fin = null,
    ) {}
}
