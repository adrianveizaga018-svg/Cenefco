<?php
namespace App\Application\ModulosAcademicos\Commands;
final readonly class CreateModuloAcademicoCommand {
    public function __construct(
        public int $idMod,
        public ?string $nombre,
        public ?string $descripcion,
        public ?int $posicion,
        public int $idUsReg,
    ) {}
}
