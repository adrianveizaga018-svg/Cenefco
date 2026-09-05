<?php

namespace App\Application\CampanasPublicidad\Handlers;

use App\Application\CampanasPublicidad\Commands\RegistrarMetricaCampanaCommand;
use App\Application\CampanasPublicidad\DTOs\CampanaMetricaDTO;
use App\Domain\CampanasPublicidad\Contracts\CampanaPublicidadRepositoryInterface;
use App\Domain\Gastos\Contracts\CategoriaGastoRepositoryInterface;
use App\Domain\Gastos\Contracts\GastoRepositoryInterface;
use Illuminate\Support\Facades\DB;

class RegistrarMetricaCampanaHandler
{
    private const CATEGORIA_GASTO_NOMBRE = 'Publicidad y marketing';

    public function __construct(
        private readonly CampanaPublicidadRepositoryInterface $repository,
        private readonly GastoRepositoryInterface $gastoRepository,
        private readonly CategoriaGastoRepositoryInterface $categoriaGastoRepository,
    ) {}

    public function handle(RegistrarMetricaCampanaCommand $c): CampanaMetricaDTO
    {
        return DB::transaction(function () use ($c) {
            $metrica = $this->repository->registrarMetrica($c->campana_publicidad_id, [
                'fecha_corte'          => $c->fecha_corte,
                'alcance'              => $c->alcance,
                'impresiones'          => $c->impresiones,
                'frecuencia'           => $c->frecuencia,
                'clics_enlace'         => $c->clics_enlace,
                'ctr'                  => $c->ctr,
                'cpc'                  => $c->cpc,
                'cpm'                  => $c->cpm,
                'resultados'           => $c->resultados,
                'tipo_resultado'       => $c->tipo_resultado,
                'costo_por_resultado'  => $c->costo_por_resultado,
                'gasto_periodo'        => $c->gasto_periodo,
                'fuente'               => $c->fuente,
                'notas'                => $c->notas,
            ]);

            if ($c->gasto_periodo !== null && $c->gasto_periodo > 0) {
                $categoriaId = $this->categoriaGastoRepository->findIdByNombre(self::CATEGORIA_GASTO_NOMBRE);

                if ($categoriaId) {
                    $campana = $this->repository->findById($c->campana_publicidad_id);

                    $this->gastoRepository->create([
                        'categoria_gasto_id'     => $categoriaId,
                        'concepto'               => "Campaña: {$campana->nombre} — corte {$c->fecha_corte}",
                        'monto'                  => $c->gasto_periodo,
                        'fecha'                  => $c->fecha_corte,
                        'nota'                   => 'Generado automáticamente al registrar el corte de métricas de la campaña.',
                        'campana_publicidad_id'  => $c->campana_publicidad_id,
                    ]);
                }
            }

            return $metrica;
        });
    }
}
