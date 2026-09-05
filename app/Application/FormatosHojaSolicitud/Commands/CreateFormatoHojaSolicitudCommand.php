<?php
namespace App\Application\FormatosHojaSolicitud\Commands;
final readonly class CreateFormatoHojaSolicitudCommand {
    public function __construct(
        public int $idFormatoHojaSolicitud,
        public ?string $nombre,
        public ?string $descripcion,
        public int $idUsReg,
    ) {}
}
