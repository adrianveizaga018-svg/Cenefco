<?php

namespace App\Application\Cartas\Handlers;

use App\Application\Cartas\Commands\CreateCartaCommand;
use App\Application\Cartas\DTOs\CartaDTO;
use App\Domain\Cartas\Contracts\CartaRepositoryInterface;

class CreateCartaHandler
{
    public function __construct(private readonly CartaRepositoryInterface $repository) {}

    public function handle(CreateCartaCommand $command): CartaDTO
    {
        $row = $this->repository->create([
            'id_carta'     => $command->id_carta,
            'id_us_reg'    => $command->id_us_reg,
            'num_carta'    => $command->num_carta,
            'id_us'        => $command->id_us,
            'id_plan'      => $command->id_plan,
            'nombresenor'  => $command->nombresenor,
            'nombretitulo' => $command->nombretitulo,
            'textocarta1'  => $command->textocarta1,
            'textocarta2'  => $command->textocarta2,
            'textocarta3'  => $command->textocarta3,
            'estado'       => $command->estado,
            'fecha_reg'    => now(),
        ]);

        return CartaDTO::fromRow($row);
    }
}
