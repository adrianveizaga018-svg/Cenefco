<?php

namespace App\Infrastructure\Pagos\Observers;

use App\Application\Inscripciones\Services\InscripcionCuotasService;
use App\Infrastructure\Pagos\Models\Pago;
use Illuminate\Support\Facades\DB;

/**
 * Todo pago creado, editado, verificado, observado o anulado por Eloquent
 * recalcula el cronograma de su inscripción (y el de la anterior si cambió de inscripción).
 * Los registros por DB::table (Caja, Cobranzas, transferencias) sincronizan por su cuenta.
 */
class PagoObserver
{
    public function saved(Pago $pago): void
    {
        $this->sincronizar([$pago->id_ins, $pago->getOriginal('id_ins')]);
    }

    public function deleted(Pago $pago): void
    {
        $this->sincronizar([$pago->id_ins]);
    }

    private function sincronizar(array $ids): void
    {
        $ids = array_unique(array_filter(array_map('intval', $ids)));
        if (! $ids) {
            return;
        }

        DB::afterCommit(function () use ($ids) {
            $servicio = app(InscripcionCuotasService::class);
            foreach ($ids as $idIns) {
                try {
                    $servicio->sincronizar($idIns);
                } catch (\Throwable $e) {
                    report($e); // un fallo al recalcular nunca debe deshacer el pago
                }
            }
        });
    }
}
