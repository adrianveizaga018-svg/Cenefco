<?php

namespace App\Application\Pagos\Handlers;

use App\Application\Pagos\Commands\ResolverDevolucionCommand;
use App\Application\Pagos\DTOs\DevolucionDTO;
use App\Domain\Pagos\Contracts\DevolucionRepositoryInterface;
use App\Domain\Pagos\Exceptions\DevolucionEstadoInvalidoException;
use App\Domain\Pagos\Exceptions\DevolucionNotFoundException;
use Illuminate\Support\Facades\DB;

class ResolverDevolucionHandler
{
    public function __construct(
        private readonly DevolucionRepositoryInterface $repository,
    ) {}

    public function handle(ResolverDevolucionCommand $command): DevolucionDTO
    {
        return DB::transaction(function () use ($command) {

            $actual = $this->repository->findByIdConLock($command->id);

            if (! $actual) {
                throw new DevolucionNotFoundException($command->id);
            }

            if ($actual->estado !== 'pendiente') {
                throw new DevolucionEstadoInvalidoException($actual->estado);
            }

            $model = $this->repository->update($command->id, [
                'estado'          => $command->estado,
                'nota_respuesta'  => $command->notaRespuesta,
                'fecha_respuesta' => now()->toDateTimeString(),
                'updated_at'      => now(),
            ]);

            return DevolucionDTO::fromModel($model);
        });
    }
}
