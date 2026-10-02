<?php

namespace App\Application\RedesSociales\Handlers;

use App\Application\RedesSociales\Commands\UpdateRedSocialCommand;
use App\Application\RedesSociales\DTOs\RedSocialDTO;
use App\Domain\RedesSociales\Contracts\RedSocialRepositoryInterface;
use App\Shared\Kernel\Contracts\CommandHandlerInterface;
use App\Shared\Kernel\Contracts\CommandInterface;

class UpdateRedSocialHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly RedSocialRepositoryInterface $repository,
    ) {}

    public function handle(CommandInterface $command): RedSocialDTO
    {
        
        $data = array_filter([
            'red'            => $command->red,
            'url'            => $command->url,
            'nombre_display' => $command->nombre_display,
            'icono_clase'    => $command->icono_clase,
            'pixel_id'       => $command->pixel_id,
            'mostrar_footer' => $command->mostrar_footer,
            'mostrar_header' => $command->mostrar_header,
            'activo'         => $command->activo,
            'orden'          => $command->orden,
        ], fn ($v) => $v !== null);

        return $this->repository->update($command->id, $data);
    }
}
