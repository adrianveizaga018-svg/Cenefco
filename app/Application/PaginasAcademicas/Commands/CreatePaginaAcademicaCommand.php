<?php
namespace App\Application\PaginasAcademicas\Commands;
final readonly class CreatePaginaAcademicaCommand {
    public function __construct(
        public int $idPagina,
        public ?string $nombre,
        public ?string $descripcion,
        public int $idUsReg,
    ) {}
}
