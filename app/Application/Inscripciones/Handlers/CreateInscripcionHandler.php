<?php

namespace App\Application\Inscripciones\Handlers;

use App\Application\Inscripciones\Commands\CreateInscripcionCommand;
use App\Application\Inscripciones\DTOs\InscripcionDTO;
use App\Domain\Inscripciones\Contracts\InscripcionRepositoryInterface;
use App\Domain\Inscripciones\Exceptions\InscripcionDuplicadaException;
use Illuminate\Support\Facades\DB;

class CreateInscripcionHandler
{
    public function __construct(
        private readonly InscripcionRepositoryInterface $repository
    ) {}

    public function handle(CreateInscripcionCommand $command): InscripcionDTO
    {
        if ($this->repository->existeInscripcionActiva($command->id_us, $command->id_imp)) {
            throw new InscripcionDuplicadaException();
        }

        try {
            return DB::transaction(function () use ($command) {
                DB::table('t_imparte')->where('id_imp', $command->id_imp)->lockForUpdate()->first();

                return $this->repository->create([
                    'id_us_reg'       => $command->id_us_reg ?? 0,
                    'fecha_ins'       => $command->fecha_ins ?? now()->toDateString(),
                    'id_us'           => $command->id_us,
                    'id_imp'          => $command->id_imp,
                    'id_plan'         => $command->id_plan,
                    'observacion_ins' => $command->observacion_ins,
                    'observacion'     => $command->observacion,
                    'periodo'         => $command->periodo,
                    'gestion'         => $command->gestion,
                    'estado'          => $command->estado,
                    'fecha_reg'       => now(),
                    'id_vendedor'     => $command->idVendedor,
                    'canal_venta'     => $command->canalVenta ?? 'admin',
                ]);
            });
        } catch (\Throwable $e) {
            if ($this->esViolacionInscripcionActivaDuplicada($e)) {
                throw new InscripcionDuplicadaException();
            }

            throw $e;
        }
    }

    private function esViolacionInscripcionActivaDuplicada(\Throwable $e): bool
    {
        if (! $e instanceof \Illuminate\Database\QueryException) {
            return false;
        }

        $sqlState = $e->errorInfo[0] ?? null;
        if ($sqlState !== '23505') {
            return false;
        }

        return str_contains($e->getMessage(), 't_inscripcion_activa_unique');
    }
}
