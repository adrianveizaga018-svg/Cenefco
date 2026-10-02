<?php
namespace App\Application\GaleriasCategorias\Commands;
final readonly class UpdateGaleriaCategoriaCommand {
    public function __construct(
        public int $id,
        public ?string $nombre = null,
        public ?string $descripcion = null,
        public ?int $orden = null,
        public ?bool $activo = null,
    ) {}
}
