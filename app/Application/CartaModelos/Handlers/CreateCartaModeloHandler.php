<?php

namespace App\Application\CartaModelos\Handlers;

use App\Application\CartaModelos\Commands\CreateCartaModeloCommand;
use App\Application\CartaModelos\DTOs\CartaModeloDTO;
use App\Domain\CartaModelos\Contracts\CartaModeloRepositoryInterface;

class CreateCartaModeloHandler
{
    public function __construct(private readonly CartaModeloRepositoryInterface $repository) {}

    public function handle(CreateCartaModeloCommand $command): CartaModeloDTO
    {
        $row = $this->repository->create([
            'id_cartamod'                  => $command->id_cartamod,
            'id_us_reg'                    => $command->id_us_reg,
            'num_cartamod'                 => $command->num_cartamod,
            'nombremodelo'                 => $command->nombremodelo,
            'textocarta'                   => $command->textocarta,
            'textocarta1'                  => $command->textocarta1,
            'textocarta3'                  => $command->textocarta3,
            'texto_carta'                  => $command->texto_carta,
            'usar_encabezado_pie_estandar' => $command->usar_encabezado_pie_estandar,
            'estado'                       => $command->estado,
            'fecha_reg'                    => now(),
        ]);

        return CartaModeloDTO::fromRow($row);
    }
}
