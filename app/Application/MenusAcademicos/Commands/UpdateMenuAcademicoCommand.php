<?php
namespace App\Application\MenusAcademicos\Commands;
final readonly class UpdateMenuAcademicoCommand {
    public function __construct(
        public int $idMen,
        public ?int $idMod,
        public ?string $nombre,
        public ?string $descripcion,
    ) {}
}
