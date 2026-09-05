<?php
namespace App\Application\GaleriasVideos\Handlers;
use App\Application\GaleriasVideos\Commands\CreateGaleriaVideoCommand;
use App\Application\GaleriasVideos\DTOs\GaleriaVideoDTO;
use App\Domain\GaleriasVideos\Contracts\GaleriaVideoRepositoryInterface;
class CreateGaleriaVideoHandler {
    public function __construct(private readonly GaleriaVideoRepositoryInterface $repository) {}
    public function handle(CreateGaleriaVideoCommand $c): GaleriaVideoDTO {
        return $this->repository->create([
            'titulo' => $c->titulo,
            'descripcion' => $c->descripcion,
            'plataforma' => $c->plataforma,
            'url_video' => $c->url_video,
            'video_id' => $c->video_id,
            'miniatura_url' => $c->miniatura_url,
            'duracion' => $c->duracion,
            'tipo' => $c->tipo,
            'programa_id' => $c->programa_id,
            'destacado' => $c->destacado,
            'orden' => $c->orden,
            'vistas' => $c->vistas,
            'activo' => $c->activo,
        ]);
    }
}
