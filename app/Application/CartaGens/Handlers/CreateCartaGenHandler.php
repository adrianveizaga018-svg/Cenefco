<?php

namespace App\Application\CartaGens\Handlers;

use App\Application\CartaGens\Commands\CreateCartaGenCommand;
use App\Application\CartaGens\DTOs\CartaGenDTO;
use App\Domain\CartaGens\Contracts\CartaGenRepositoryInterface;

class CreateCartaGenHandler
{
    public function __construct(private readonly CartaGenRepositoryInterface $repository) {}

    public function handle(CreateCartaGenCommand $command): CartaGenDTO
    {
        $row = $this->repository->create([
            'id_cartagen'                  => $command->id_cartagen,
            'id_us_reg'                    => $command->id_us_reg,
            'num_carta'                    => $command->num_carta,
            'id_us'                        => $command->id_us,
            'id_cartamod'                  => $command->id_cartamod,
            'textocarta'                   => $command->textocarta,
            'textocarta1'                  => $command->textocarta1,
            'textocarta3'                  => $command->textocarta3,
            'usar_encabezado_pie_estandar' => $command->usar_encabezado_pie_estandar,
            'cp_nro_contrato'              => $command->cp_nro_contrato,
            'cp_gestion_contrato'          => $command->cp_gestion_contrato,
            'estado'                       => $command->estado,
            'fecha_reg'                    => now(),
        ]);

        return CartaGenDTO::fromRow($row);
    }
}
