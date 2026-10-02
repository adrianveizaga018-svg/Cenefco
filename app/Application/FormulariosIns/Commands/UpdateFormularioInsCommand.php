<?php
namespace App\Application\FormulariosIns\Commands;
final readonly class UpdateFormularioInsCommand {
    public function __construct(
        public int $idFormins,
        public ?int $idImp,
        public ?string $nombre,
        public ?string $descripcion,
    ) {}
}
