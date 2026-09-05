<?php
namespace App\Application\GaleriasCategorias\Commands;
final readonly class CreateGaleriaCategoriaCommand {
    public function __construct(
        public string $nombre,
        public ?string $descripcion = null,
        public int $orden = 0,
        public bool $activo = true,
    ) {}
}
