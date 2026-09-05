<?php

namespace App\Application\CertPlantillaCampos\Handlers;

use App\Application\CertPlantillaCampos\Commands\CreateCertPlantillaCampoCommand;
use App\Application\CertPlantillaCampos\DTOs\CertPlantillaCampoDTO;
use App\Domain\CertPlantillaCampos\Contracts\CertPlantillaCampoRepositoryInterface;
use Illuminate\Database\UniqueConstraintViolationException;

class CreateCertPlantillaCampoHandler
{
    public function __construct(
        private readonly CertPlantillaCampoRepositoryInterface $repository
    ) {}

    public function handle(CreateCertPlantillaCampoCommand $command): CertPlantillaCampoDTO
    {
        try {
        $row = $this->repository->create([
            'plantilla_id' => $command->plantilla_id,
            'clave'        => $command->clave,
            'etiqueta'     => $command->etiqueta,
            'tipo'         => $command->tipo,
            'pos_x_pct'    => $command->pos_x_pct,
            'pos_y_pct'    => $command->pos_y_pct,
            'ancho_pct'    => $command->ancho_pct,
            'alto_pct'     => $command->alto_pct,
            'fuente'       => $command->fuente,
            'tamano_pt'    => $command->tamano_pt,
            'color'        => $command->color,
            'alineacion'   => $command->alineacion,
            'negrita'      => $command->negrita,
            'cursiva'      => $command->cursiva,
            'mayusculas'   => $command->mayusculas,
            'valor_fijo'   => $command->valor_fijo,
            'activo'       => $command->activo,
            'orden'        => $command->orden,
        ]);

        return CertPlantillaCampoDTO::fromRow($row);
        } catch (UniqueConstraintViolationException) {
            abort(422, "Ya existe un campo con la clave \"{$command->clave}\" en esta plantilla.");
        }
    }
}
