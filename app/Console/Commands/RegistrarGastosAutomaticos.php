<?php

namespace App\Console\Commands;

use App\Application\Gastos\Services\GastosAutomaticosService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Registra como gasto lo gastado en Facebook por campaña y por mes, y los pagos a docentes que
 * aún no tengan su gasto. Se puede correr las veces que haga falta: actualiza, no duplica.
 */
class RegistrarGastosAutomaticos extends Command
{
    protected $signature = 'gastos:automaticos
        {--desde= : Primer mes a recalcular (AAAA-MM). Por defecto, el mes anterior}
        {--todo : Recalcular desde que empezó la campaña más antigua}';

    protected $description = 'Registra como gastos la publicidad de Facebook y los pagos a docentes';

    public function handle(GastosAutomaticosService $gastos): int
    {
        $desde = match (true) {
            (bool) $this->option('todo') => Carbon::parse(DB::table('campana_publicidad')
                ->whereIn('id', DB::table('campana_metrica')->select('campana_publicidad_id'))
                ->min('fecha_inicio') ?? DB::table('campana_metrica')->min('fecha_corte') ?? now()),
            (bool) $this->option('desde') => Carbon::createFromFormat('Y-m-d', $this->option('desde').'-01'),
            default => now()->subMonthNoOverflow(),
        };

        $publicidad = $gastos->sincronizarPublicidad($desde->startOfMonth());
        $docentes = $gastos->completarPagosDocentes();

        $this->info("Publicidad: {$publicidad} gasto(s) mensuales actualizados desde {$desde->format('m/Y')}. Pagos a docentes completados: {$docentes}.");

        return self::SUCCESS;
    }
}
