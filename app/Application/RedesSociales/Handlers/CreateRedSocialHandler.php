<?php

namespace App\Application\RedesSociales\Handlers;

use App\Application\RedesSociales\Commands\CreateRedSocialCommand;
use App\Application\RedesSociales\DTOs\RedSocialDTO;
use App\Domain\RedesSociales\Contracts\RedSocialRepositoryInterface;

class CreateRedSocialHandler
{
    public function __construct(private readonly RedSocialRepositoryInterface $repository) {}

    public function handle(CreateRedSocialCommand $command): RedSocialDTO
    {
        return $this->repository->create([
            'red'            => $command->red,
            'nombre_display' => $command->nombre_display,
            'url'            => $command->url,
            'icono_clase'    => $command->icono_clase,
            'pixel_id'       => $command->pixel_id,
            'mostrar_footer' => $command->mostrar_footer,
            'mostrar_header' => $command->mostrar_header,
            'activo'         => $command->activo,
            'orden'          => $command->orden,
        ]);
    }
}
