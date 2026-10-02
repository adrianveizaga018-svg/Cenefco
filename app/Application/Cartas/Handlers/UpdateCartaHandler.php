<?php

namespace App\Application\Cartas\Handlers;

use App\Application\Cartas\Commands\UpdateCartaCommand;
use App\Application\Cartas\DTOs\CartaDTO;
use App\Domain\Cartas\Contracts\CartaRepositoryInterface;

class UpdateCartaHandler
{
    public function __construct(private readonly CartaRepositoryInterface $repository) {}

    public function handle(UpdateCartaCommand $command): CartaDTO
    {
        $this->repository->findById($command->id);

        $changes = array_filter([
            'nombresenor'  => $command->nombresenor,
            'nombretitulo' => $command->nombretitulo,
            'textocarta1'  => $command->textocarta1,
            'textocarta2'  => $command->textocarta2,
            'textocarta3'  => $command->textocarta3,
            'estado'       => $command->estado,
        ], fn ($v) => $v !== null);

        $this->repository->update($command->id, $changes);

        return CartaDTO::fromRow($this->repository->findById($command->id));
    }
}
