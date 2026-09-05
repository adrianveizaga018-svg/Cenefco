<?php
namespace App\Application\GaleriasVideos\Commands;
final readonly class CreateGaleriaVideoCommand {
    public function __construct(
        public string $titulo,
        public string $url_video,
        public ?string $descripcion = null,
        public string $plataforma = 'youtube',
        public ?string $video_id = null,
        public ?string $miniatura_url = null,
        public ?string $duracion = null,
        public ?string $tipo = null,
        public ?int $programa_id = null,
        public bool $destacado = false,
        public int $orden = 0,
        public int $vistas = 0,
        public bool $activo = true,
    ) {}
}
