<?php

namespace App\Application\SueldosDocentes\Handlers;

use App\Application\SueldosDocentes\Commands\RegistrarPagoSueldoCommand;
use App\Application\SueldosDocentes\DTOs\PagoSueldoDTO;
use App\Domain\SueldosDocentes\Contracts\SueldoDocenteRepositoryInterface;

class RegistrarPagoSueldoHandler
{
    public function __construct(
        private readonly SueldoDocenteRepositoryInterface $repository,
    ) {}

    public function handle(RegistrarPagoSueldoCommand $command): PagoSueldoDTO
    {
        return $this->repository->registrarPago($command->idSueldo, [
            'monto_pagado'        => $command->montoPagado,
            'fecha_pago'          => $command->fechaPago,
            'nro_comprobante'     => $command->nroComprobante,
            'observacion'         => $command->observacion,
            'comprobante_archivo' => $command->comprobanteArchivo,
            'estado'              => 1,
        ]);
    }
}
