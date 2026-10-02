<?php

namespace App\Application\Pagos\Handlers;

use App\Application\Pagos\Commands\CreateDevolucionCommand;
use App\Application\Pagos\DTOs\DevolucionDTO;
use App\Domain\Inscripciones\Contracts\InscripcionRepositoryInterface;
use App\Domain\Pagos\Contracts\DevolucionRepositoryInterface;
use App\Domain\Pagos\Exceptions\DevolucionMontoExcedeDisponibleException;
use App\Domain\Ventas\Contracts\VentaRepositoryInterface;

class CreateDevolucionHandler
{
    public function __construct(
        private readonly DevolucionRepositoryInterface  $repository,
        private readonly InscripcionRepositoryInterface $inscripcionRepository,
        private readonly VentaRepositoryInterface        $ventaRepository,
    ) {}

    public function handle(CreateDevolucionCommand $command): DevolucionDTO
    {
        $inscripcion = $this->inscripcionRepository->findById($command->idIns);

        $venta = $this->ventaRepository->findById($command->idIns);
        $totalPagado = $venta->resumen->total_pagado ?? 0.0;

        $totalYaDevuelto = collect($this->repository->findByInscripcion($command->idIns))
            ->where('estado', 'aprobada')
            ->sum('monto');

        $disponible = max(0.0, $totalPagado - $totalYaDevuelto);

        if ($command->monto > $disponible) {
            throw new DevolucionMontoExcedeDisponibleException($command->monto, $disponible);
        }

        $model = $this->repository->create([
            'id_ins'        => $command->idIns,
            'id_us'         => $inscripcion->id_us,
            'monto'         => $command->monto,
            'motivo'        => $command->motivo,
            'documento_url' => $command->documentoUrl,
            'estado'        => 'pendiente',
        ]);

        return DevolucionDTO::fromModel($model);
    }
}
