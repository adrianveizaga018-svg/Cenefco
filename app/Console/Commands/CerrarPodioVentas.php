<?php

namespace App\Console\Commands;

use App\Application\Comisiones\Services\RankingVentasService;
use Illuminate\Console\Command;

/** El 1 de cada mes: guarda el podio del mes anterior y felicita a los ganadores. */
class CerrarPodioVentas extends Command
{
    protected $signature = 'ventas:podio {--mes= : Mes a cerrar (AAAA-MM). Por defecto, el mes anterior}';

    protected $description = 'Guarda el podio de vendedores del mes y envía las felicitaciones';

    public function handle(RankingVentasService $ranking): int
    {
        $periodo = $this->option('mes') ?: now()->subMonthNoOverflow()->format('Y-m');
        $resultado = $ranking->cerrarMes($periodo);

        if (! $resultado['podio']) {
            $this->info("Sin ventas en {$periodo}: no hay podio.");
        } elseif (! $resultado['anunciado']) {
            $this->info("El podio de {$periodo} ya se había anunciado.");
        } else {
            $this->info("Podio de {$periodo}: ".collect($resultado['podio'])->map(fn ($p) => "{$p['puesto']}° {$p['nombre']}")->join(', '));
        }

        return self::SUCCESS;
    }
}
