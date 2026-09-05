<?php
namespace App\Application\PaginasAcademicas\Commands;
final readonly class UpdatePaginaAcademicaCommand {
    public function __construct(
        public int $idPagina,
        public ?string $nombre,
        public ?string $descripcion,
    ) {}
}
