<?php
namespace App\Application\MenusAcademicos\Commands;
final readonly class CreateMenuAcademicoCommand {
    public function __construct(
        public int $idMen,
        public ?int $idMod,
        public ?string $nombre,
        public ?string $descripcion,
        public int $idUsReg,
    ) {}
}
