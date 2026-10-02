<?php
namespace App\Application\FormulariosAcademicos\Commands;
final readonly class UpdateFormularioAcademicoCommand {
    public function __construct(
        public int $idFormulario,
        public ?string $nombre,
        public ?string $descripcion,
    ) {}
}
