<?php
namespace App\Application\WhatsappGrupos\Commands;
final readonly class UpdateWhatsappGrupoCommand {
    public function __construct(
        public int $id,
        public ?int $imparte_id = null,
        public ?string $nombre = null,
        public ?string $enlace_invitacion = null,
        public ?int $capacidad_maxima = null,
        public ?int $miembros_actuales = null,
        public ?string $descripcion = null,
        public ?bool $activo = null,
        public ?int $orden = null,
        public ?string $fecha_expiracion_enlace = null,
    ) {}
}
