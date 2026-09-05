<?php
namespace App\Application\FormatosHojaSolicitud\Commands;
final readonly class UpdateFormatoHojaSolicitudCommand {
    public function __construct(
        public int $idFormatoHojaSolicitud,
        public ?string $nombre,
        public ?string $descripcion,
    ) {}
}
