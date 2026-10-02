<?php
namespace App\Application\ModulosAcademicos\Commands;
final readonly class UpdateModuloAcademicoCommand {
    public function __construct(
        public int $idMod,
        public ?string $nombre,
        public ?string $descripcion,
        public ?int $posicion,
    ) {}
}
