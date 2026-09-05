<?php
namespace App\Application\GaleriasVideos\Commands;
final readonly class UpdateGaleriaVideoCommand {
    public function __construct(
        public int $id,
        public ?string $titulo = null,
        public ?string $descripcion = null,
        public ?string $plataforma = null,
        public ?string $url_video = null,
        public ?string $video_id = null,
        public ?string $miniatura_url = null,
        public ?string $duracion = null,
        public ?string $tipo = null,
        public ?int $programa_id = null,
        public ?bool $destacado = null,
        public ?int $orden = null,
        public ?int $vistas = null,
        public ?bool $activo = null,
    ) {}
}
