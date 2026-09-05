<?php

namespace App\Application\CartaGens\Handlers;

use App\Application\CartaGens\Commands\UpdateCartaGenCommand;
use App\Application\CartaGens\DTOs\CartaGenDTO;
use App\Domain\CartaGens\Contracts\CartaGenRepositoryInterface;

class UpdateCartaGenHandler
{
    public function __construct(private readonly CartaGenRepositoryInterface $repository) {}

    public function handle(UpdateCartaGenCommand $command): CartaGenDTO
    {
        $this->repository->findById($command->id);

        $changes = array_filter([
            'textocarta'                   => $command->textocarta,
            'textocarta1'                  => $command->textocarta1,
            'textocarta3'                  => $command->textocarta3,
            'usar_encabezado_pie_estandar' => $command->usar_encabezado_pie_estandar,
            'cp_nro_contrato'              => $command->cp_nro_contrato,
            'cp_gestion_contrato'          => $command->cp_gestion_contrato,
            'estado'                       => $command->estado,
        ], fn ($v) => $v !== null);

        $this->repository->update($command->id, $changes);

        return CartaGenDTO::fromRow($this->repository->findById($command->id));
    }
}
