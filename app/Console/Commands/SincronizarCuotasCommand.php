<?php

namespace App\Console\Commands;

use App\Application\Inscripciones\Services\InscripcionCuotasService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Crea el cronograma que falte y recalcula lo pagado de cada cuota en todas las
 * inscripciones activas. Correr una vez al desplegar; después cada pago se recalcula solo.
 */
class SincronizarCuotasCommand extends Command
{
    protected $signature = 'cuotas:sincronizar {--ins= : solo esta inscripción}';

    protected $description = 'Recalcula las cuotas de las inscripciones activas a partir de sus pagos';

    public function handle(InscripcionCuotasService $cuotas): int
    {
        $ids = $this->option('ins')
            ? collect([(int) $this->option('ins')])
            : DB::table('t_inscripcion')->where('estado', 1)->orderBy('id_ins')->pluck('id_ins');

        $conCronograma = 0;
        $errores = 0;
        $this->withProgressBar($ids, function ($idIns) use ($cuotas, &$conCronograma, &$errores) {
            try {
                if ($cuotas->sincronizar((int) $idIns)) {
                    $conCronograma++;
                }
            } catch (\Throwable $e) {
                $errores++;
                report($e);
            }
        });

        $this->newLine();
        $this->info("{$ids->count()} inscripciones revisadas · {$conCronograma} con cronograma · {$errores} con error.");

        return $errores ? self::FAILURE : self::SUCCESS;
    }
}
