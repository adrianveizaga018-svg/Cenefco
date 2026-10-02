<?php

namespace App\Application\CompromisosCobro\Handlers;

use App\Application\CompromisosCobro\Commands\CancelarCompromisoCobroCommand;
use App\Application\CompromisosCobro\DTOs\CompromisoCobroDTO;
use App\Domain\CompromisosCobro\Contracts\CompromisoCobroRepositoryInterface;
use App\Domain\CompromisosCobro\Enums\EstadoCompromisoCobro;
use Illuminate\Support\Facades\DB;

class CancelarCompromisoCobroHandler
{
    public function __construct(private readonly CompromisoCobroRepositoryInterface $repository) {}

    public function handle(CancelarCompromisoCobroCommand $c): CompromisoCobroDTO
    {
        return DB::transaction(function () use ($c) {
            $this->repository->registrarLog($c->id, [
                'tipo_evento'    => 'cancelado',
                'observacion'    => $c->observacion,
                'registrado_por' => $c->registradoPor,
            ]);

            return $this->repository->update($c->id, [
                'estado' => EstadoCompromisoCobro::Cancelado->value,
            ]);
        });
    }
}
