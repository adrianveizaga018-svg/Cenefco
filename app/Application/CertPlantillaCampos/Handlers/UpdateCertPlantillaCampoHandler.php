<?php

namespace App\Application\CertPlantillaCampos\Handlers;

use App\Application\CertPlantillaCampos\Commands\UpdateCertPlantillaCampoCommand;
use App\Application\CertPlantillaCampos\DTOs\CertPlantillaCampoDTO;
use App\Domain\CertPlantillaCampos\Contracts\CertPlantillaCampoRepositoryInterface;

class UpdateCertPlantillaCampoHandler
{
    public function __construct(
        private readonly CertPlantillaCampoRepositoryInterface $repository
    ) {}

    public function handle(UpdateCertPlantillaCampoCommand $command): CertPlantillaCampoDTO
    {
        $changes = array_filter([
            'clave'      => $command->clave,
            'etiqueta'   => $command->etiqueta,
            'tipo'       => $command->tipo,
            'pos_x_pct'  => $command->pos_x_pct,
            'pos_y_pct'  => $command->pos_y_pct,
            'ancho_pct'  => $command->ancho_pct,
            'alto_pct'   => $command->alto_pct,
            'fuente'     => $command->fuente,
            'tamano_pt'  => $command->tamano_pt,
            'color'      => $command->color,
            'alineacion' => $command->alineacion,
            'negrita'    => $command->negrita,
            'cursiva'    => $command->cursiva,
            'mayusculas' => $command->mayusculas,
            'valor_fijo' => $command->valor_fijo,
            'activo'     => $command->activo,
            'orden'      => $command->orden,
        ], fn ($v) => $v !== null);

        if (empty($changes)) {
            return CertPlantillaCampoDTO::fromRow($this->repository->findById($command->id));
        }

        $row = $this->repository->update($command->id, $changes);

        return CertPlantillaCampoDTO::fromRow($row);
    }
}
