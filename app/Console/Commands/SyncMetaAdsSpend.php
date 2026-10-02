<?php

namespace App\Console\Commands;

use App\Infrastructure\Meta\MetaAdsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncMetaAdsSpend extends Command
{
    protected $signature = 'meta:sync-gastos
        {--cuenta= : ID de cuenta publicitaria a sincronizar (ej: act_1652384699141172)}
        {--desde= : Sólo sincronizar campañas con fecha_inicio >= esta fecha (YYYY-MM-DD)}
        {--hasta= : Sólo sincronizar campañas con fecha_inicio <= esta fecha (YYYY-MM-DD)}';
    protected $description = 'Sincroniza el gasto (spend) de campañas de Facebook Ads a la base de datos';

    public function handle(MetaAdsService $metaAds)
    {
        $this->info('Iniciando sincronización con Meta Ads...');

        // Solo buscar campañas que sean de Facebook/Meta, y que tengan ID externo
        $q = DB::table('campana_publicidad')
            ->whereIn('plataforma', ['facebook', 'meta_ads'])
            ->whereNotNull('id_campana_externa')
            ->where('id_campana_externa', '!=', '');

        // Solo sincronizar campañas de cuentas que están configuradas en .env
        $cuentasConfiguradas = array_map(
            fn($c) => str_starts_with(trim($c), 'act_') ? trim($c) : 'act_' . trim($c),
            explode(',', env('META_AD_ACCOUNT_IDS', '1652384699141172,1420958161864793'))
        );

        if ($this->option('cuenta')) {
            $cuentaFiltro = $this->option('cuenta');
            if (!str_starts_with($cuentaFiltro, 'act_')) $cuentaFiltro = 'act_' . $cuentaFiltro;
            $q->where('cuenta_externa_id', $cuentaFiltro);
        } else {
            $q->whereIn('cuenta_externa_id', $cuentasConfiguradas);
        }
        if ($this->option('desde')) {
            $q->where('fecha_inicio', '>=', $this->option('desde'));
        }
        if ($this->option('hasta')) {
            $q->where('fecha_inicio', '<=', $this->option('hasta'));
        }

        $campanas = $q->get();

        $this->info("Se encontraron {$campanas->count()} campañas para sincronizar.");

        $actualizadas = 0;
        $errores = 0;

        // Pre-cargar los estados de todas las cuentas involucradas para no hacer 1 petición por campaña
        $statusesPorCuenta = [];
        $cuentasUnicas = $campanas->pluck('cuenta_externa_id')->filter()->unique();
        foreach ($cuentasUnicas as $cId) {
            $statusesPorCuenta[$cId] = $metaAds->getAccountCampaignStatuses($cId);
        }

        foreach ($campanas as $campana) {
            $this->line("Consultando campaña: {$campana->nombre} (ID Externa: {$campana->id_campana_externa})");
            
            $insights = $metaAds->getCampaignInsights(
                $campana->id_campana_externa,
                $this->option('desde') ?: null,
                $this->option('hasta') ?: null
            );

            if ($insights !== null) {
                // Suponemos que la cuenta de Meta devuelve en dólares y la TC es aprox 7. 
                // En un sistema real se puede configurar dinámicamente en .env
                $tipoCambio = env('TIPO_CAMBIO_USD_BOB', 6.96);
                $spendUsd = $insights['spend'];
                $spendBob = $spendUsd * $tipoCambio;

                $updateData = [
                    'presupuesto_usd' => $spendUsd,
                    'presupuesto_bob' => $spendBob,
                    'updated_at' => now(),
                ];
                if (isset($insights['leads']) && $insights['leads'] !== null) {
                    $updateData['leads'] = $insights['leads'];
                }

                // Actualizar estado si lo tenemos pre-cargado
                if ($campana->cuenta_externa_id && isset($statusesPorCuenta[$campana->cuenta_externa_id][$campana->id_campana_externa])) {
                    $updateData['estado'] = $statusesPorCuenta[$campana->cuenta_externa_id][$campana->id_campana_externa];
                }

                DB::table('campana_publicidad')
                    ->where('id', $campana->id)
                    ->update($updateData);

                // Registrar la métrica histórica si hubo clicks/impresiones (opcional)
                DB::table('campana_metrica')->updateOrInsert(
                    ['campana_publicidad_id' => $campana->id, 'fecha_corte' => now()->toDateString()],
                    [
                        'gasto_periodo' => $spendBob,
                        'clics_enlace' => $insights['clicks'],
                        'impresiones' => $insights['impressions'],
                        'alcance' => $insights['reach'] ?? null,
                        'resultados' => $insights['leads'] ?? null,
                        'tipo_resultado' => isset($insights['leads']) ? 'leads' : null,
                        'fuente' => 'api_meta',
                        'created_at' => now(),
                    ]
                );

                $this->info("-> Gasto actualizado: \${$spendUsd} USD / Bs. {$spendBob}");
                $actualizadas++;
            } else {
                $this->error("-> Falló al consultar datos para esta campaña.");
                $errores++;
            }
        }

        $this->info("Sincronización terminada. Actualizadas: {$actualizadas}, Errores: {$errores}.");
        Log::info("Meta Ads Sync", ['actualizadas' => $actualizadas, 'errores' => $errores]);
        
        return self::SUCCESS;
    }
}