<?php
namespace App\Application\Historiales\Commands;
final readonly class UpdateHistorialCommand {
    public function __construct(
        public int $idHistorial,
        public ?int $idUs,
        public ?int $idTiporeferencia,
        public ?int $idTipohistorial,
        public ?string $descripcion,
    ) {}
}
