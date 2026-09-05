<?php

namespace App\Application\CartaModelos\Handlers;

use App\Application\CartaModelos\Commands\UpdateCartaModeloCommand;
use App\Application\CartaModelos\DTOs\CartaModeloDTO;
use App\Domain\CartaModelos\Contracts\CartaModeloRepositoryInterface;

class UpdateCartaModeloHandler
{
    public function __construct(private readonly CartaModeloRepositoryInterface $repository) {}

    public function handle(UpdateCartaModeloCommand $command): CartaModeloDTO
    {
        $this->repository->findById($command->id);

        $changes = array_filter([
            'nombremodelo'                 => $command->nombremodelo,
            'textocarta'                   => $command->textocarta,
            'textocarta1'                  => $command->textocarta1,
            'textocarta3'                  => $command->textocarta3,
            'texto_carta'                  => $command->texto_carta,
            'usar_encabezado_pie_estandar' => $command->usar_encabezado_pie_estandar,
            'estado'                       => $command->estado,
        ], fn ($v) => $v !== null);

        $this->repository->update($command->id, $changes);

        return CartaModeloDTO::fromRow($this->repository->findById($command->id));
    }
}
