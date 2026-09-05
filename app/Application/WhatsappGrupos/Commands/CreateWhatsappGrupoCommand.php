<?php
namespace App\Application\WhatsappGrupos\Commands;
final readonly class CreateWhatsappGrupoCommand {
    public function __construct(
        public int $imparte_id,
        public string $nombre,
        public string $enlace_invitacion,
        public ?int $capacidad_maxima = null,
        public int $miembros_actuales = 0,
        public ?string $descripcion = null,
        public bool $activo = true,
        public int $orden = 0,
        public ?string $fecha_expiracion_enlace = null,
    ) {}
}
