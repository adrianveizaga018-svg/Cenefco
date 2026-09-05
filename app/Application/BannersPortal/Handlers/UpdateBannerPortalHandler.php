<?php

namespace App\Application\BannersPortal\Handlers;

use App\Application\BannersPortal\Commands\UpdateBannerPortalCommand;
use App\Application\BannersPortal\DTOs\BannerPortalDTO;
use App\Domain\BannersPortal\Contracts\BannerPortalRepositoryInterface;

class UpdateBannerPortalHandler
{
    public function __construct(private readonly BannerPortalRepositoryInterface $repository) {}

    public function handle(UpdateBannerPortalCommand $command): BannerPortalDTO
    {
        $data = array_filter([
            'titulo'           => $command->titulo,
            'subtitulo'        => $command->subtitulo,
            'imagen_url'       => $command->imagen_url,
            'imagen_alt'       => $command->imagen_alt,
            'imagen_movil_url' => $command->imagen_movil_url,
            'enlace_url'       => $command->enlace_url,
            'enlace_texto'     => $command->enlace_texto,
            'enlace_target'    => $command->enlace_target,
            'enlace_url_2'     => $command->enlace_url_2,
            'enlace_texto_2'   => $command->enlace_texto_2,
            'posicion'         => $command->posicion,
            'fecha_inicio'     => $command->fecha_inicio,
            'fecha_fin'        => $command->fecha_fin,
            'activo'           => $command->activo,
            'orden'            => $command->orden,
        ], fn ($v) => $v !== null);

        return $this->repository->update($command->id, $data);
    }
}
