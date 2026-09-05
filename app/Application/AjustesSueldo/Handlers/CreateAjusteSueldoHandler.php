<?php

namespace App\Application\AjustesSueldo\Handlers;

use App\Application\AjustesSueldo\Commands\CreateAjusteSueldoCommand;
use App\Application\AjustesSueldo\DTOs\AjusteSueldoDTO;
use App\Domain\AjustesSueldo\Contracts\AjusteSueldoRepositoryInterface;
use App\Domain\AjustesSueldo\Exceptions\AjustePeriodoYaPagadoException;
use App\Domain\Planillas\Contracts\PlanillaRepositoryInterface;

class CreateAjusteSueldoHandler
{
    public function __construct(
        private readonly AjusteSueldoRepositoryInterface $repository,
        private readonly PlanillaRepositoryInterface $planillaRepository,
    ) {}

    public function handle(CreateAjusteSueldoCommand $command): AjusteSueldoDTO
    {
        if ($this->planillaRepository->existePlanillaDelMes($command->anio, $command->mes)) {
            throw new AjustePeriodoYaPagadoException($command->anio, $command->mes);
        }

        $model = $this->repository->create([
            'empleado_id'    => $command->empleadoId,
            'anio'           => $command->anio,
            'mes'            => $command->mes,
            'tipo'           => $command->tipo,
            'monto'          => $command->monto,
            'motivo'         => $command->motivo,
            'registrado_por' => $command->registradoPor,
            'aplicado'       => false,
        ]);

        return AjusteSueldoDTO::fromModel($model->loadMissing('empleado'));
    }
}
