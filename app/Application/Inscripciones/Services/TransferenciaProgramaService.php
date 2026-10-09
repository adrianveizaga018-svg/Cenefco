<?php

namespace App\Application\Inscripciones\Services;

use App\Application\Inscripciones\Queries\GetInscripcionDetalleQuery;
use App\Application\Inscripciones\QueryHandlers\GetInscripcionDetalleQueryHandler;
use App\Domain\Pagos\Contracts\PagoRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Cambio de programa de una inscripción.
 *
 *  - Se permite siempre, salvo que el estudiante ya tenga certificado emitido en el programa de origen.
 *  - Avisa (no bloquea) si el programa de origen ya empezó.
 *  - Los pagos reales SE TRASLADAN a la nueva inscripción (no se duplican): el dinero
 *    entra una sola vez en los reportes y cada traslado queda en t_pagolog.
 *  - Lo pagado se reconoce sobre las cuotas del nuevo plan; una cuota cubierta a medias
 *    queda con el saldo que falta.
 *  - Si lo pagado supera el costo del nuevo plan, el sobrante queda como devolución pendiente.
 */
class TransferenciaProgramaService
{
    public function __construct(
        private readonly GetInscripcionDetalleQueryHandler $detalleHandler,
        private readonly InscripcionCuotasService $cuotasService,
        private readonly PagoRepositoryInterface $pagos,
    ) {}

    /**
     * Calcula el cambio sin guardar nada.
     *
     * @return array{total_pagado_origen: float, total_nuevo_plan: float, saldo_pendiente: float,
     *               saldo_a_favor: float, distribucion: array, advertencias: string[]}
     */
    public function preview(int $idIns, int $idImpDestino, int $idPlanDestino): array
    {
        $origen = $this->validar($idIns, $idImpDestino, $idPlanDestino);

        $totalPagado = (float) $this->detalleHandler->handle(new GetInscripcionDetalleQuery($idIns))->resumen->total_pagado;
        [$cuotas, $costoTotal] = $this->cuotasDelPlan($idPlanDestino);

        $disponible   = $totalPagado;
        $distribucion = [];
        foreach ($cuotas as $c) {
            $acreditado = round(min($disponible, $c['monto']), 2);
            $disponible = round($disponible - $acreditado, 2);
            $distribucion[] = [
                'nro_cuota'        => $c['nro'],
                'descripcion'      => $c['descripcion'],
                'monto_cuota'      => $c['monto'],
                'monto_acreditado' => $acreditado,
                'monto_pendiente'  => round($c['monto'] - $acreditado, 2),
                'estado'           => $acreditado >= $c['monto'] ? 'pagado' : ($acreditado > 0 ? 'parcial' : 'pendiente'),
                'fecha_vencimiento'=> $c['fecha_fin'],
            ];
        }

        $advertencias = [];
        if ($origen->imparte_fecha_inicio && Carbon::parse($origen->imparte_fecha_inicio)->lte(today())) {
            $advertencias[] = 'El programa de origen ya empezó el '.Carbon::parse($origen->imparte_fecha_inicio)->format('d/m/Y').'.';
        }

        return [
            'total_pagado_origen' => round($totalPagado, 2),
            'total_nuevo_plan'    => round($costoTotal, 2),
            'saldo_pendiente'     => round(max(0, $costoTotal - $totalPagado), 2),
            'saldo_a_favor'       => round(max(0, $totalPagado - $costoTotal), 2),
            'distribucion'        => $distribucion,
            'advertencias'        => $advertencias,
        ];
    }

    /** @return array{id_ins_origen: int, id_ins_destino: int, id_devolucion: ?int, preview: array} */
    public function ejecutar(int $idIns, int $idImpDestino, int $idPlanDestino, string $motivo, int $procesadoPor): array
    {
        $preview = $this->preview($idIns, $idImpDestino, $idPlanDestino);
        $origen  = DB::table('t_inscripcion')->where('id_ins', $idIns)->first();
        $pagosOrigen = $this->idsPagosActivos($idIns);

        return DB::transaction(function () use ($idIns, $idImpDestino, $idPlanDestino, $motivo, $procesadoPor, $preview, $origen, $pagosOrigen) {
            // 1. Baja de la inscripción de origen
            DB::table('t_inscripcion')->where('id_ins', $idIns)->update([
                'estado'          => 0,
                'observacion_ins' => trim(($origen->observacion_ins ?? '')."\n[TRANSFERIDA] Motivo: {$motivo}"),
            ]);

            // 2. Nueva inscripción. t_inscripcion (tabla legada) no tiene ID autoincremental:
            //    se usa el mismo criterio que Caja, dentro de esta transacción.
            $idNueva = (int) (DB::table('t_inscripcion')->max('id_ins') ?? 0) + 1;
            DB::table('t_inscripcion')->insert([
                'id_ins'          => $idNueva,
                'id_us_reg'       => $procesadoPor,
                'id_us'           => $origen->id_us,
                'id_imp'          => $idImpDestino,
                'id_plan'         => $idPlanDestino,
                'fecha_ins'       => now()->toDateString(),
                'fecha_reg'       => now(),
                'gestion'         => now()->year,
                'periodo'         => $origen->periodo ?? ('I-'.now()->year),
                'estado'          => 1,
                'canal_venta'     => $origen->canal_venta ?? 'transferencia',
                'origen'          => 'transferencia_programa',
                'email'           => $origen->email ?? null,
                'telefono'        => $origen->telefono ?? null,
                'observacion_ins' => "[TRANSFERIDA DESDE inscripción #{$idIns}] Motivo: {$motivo}",
            ]);

            // 3. Trasladar los pagos reales (quedan como crédito a cuenta del nuevo plan)
            foreach ($pagosOrigen as $idPago) {
                $pago = DB::table('t_pago')->where('id_pago', $idPago)->first();
                if (! $pago) {
                    continue;
                }
                $this->pagos->auditarCambio($pago, 'transferencia', $procesadoPor);
                DB::table('t_pago')->where('id_pago', $idPago)->update([
                    'id_ins'           => $idNueva,
                    'id_fechapago'     => null,
                    'pago_extra'       => 1,
                    'observacion_pago' => trim(($pago->observacion_pago ?? '')." [Trasladado desde inscripción #{$idIns}]"),
                ]);
            }

            // 4. Cuotas del nuevo plan, con lo pagado ya reconocido
            // Los pagos ya se movieron a la nueva inscripción: el cronograma los reparte solo
            $this->cuotasService->generarCuotasParaInscripcion($idNueva, $idPlanDestino);

            // 5. Sobrante → devolución pendiente (alguien la aprueba o rechaza en el panel)
            $idDevolucion = null;
            if ($preview['saldo_a_favor'] > 0) {
                $idDevolucion = (int) DB::table('web_devolucion')->insertGetId([
                    'id_ins'         => $idNueva,
                    'id_us'          => $origen->id_us,
                    'monto'          => $preview['saldo_a_favor'],
                    'motivo'         => "Saldo a favor por cambio de programa (inscripción #{$idIns} → #{$idNueva}). {$motivo}",
                    'estado'         => 'pendiente',
                    'registrado_por' => $procesadoPor,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }

            // 6. Historial
            DB::table('transferencias_inscripcion')->insert([
                'id_ins_origen'     => $idIns,
                'id_ins_destino'    => $idNueva,
                'id_us'             => $origen->id_us,
                'monto_transferido' => $preview['total_pagado_origen'],
                'motivo'            => $motivo,
                'procesado_por'     => $procesadoPor,
                'created_at'        => now(),
            ]);

            return ['id_ins_origen' => $idIns, 'id_ins_destino' => $idNueva, 'id_devolucion' => $idDevolucion, 'preview' => $preview];
        });
    }

    /** Transferencias en las que participa una inscripción (para mostrar "desde"/"hacia"). */
    public function relacionadas(int $idIns): array
    {
        return [
            'desde' => DB::table('transferencias_inscripcion')->where('id_ins_destino', $idIns)->value('id_ins_origen'),
            'hacia' => DB::table('transferencias_inscripcion')->where('id_ins_origen', $idIns)->value('id_ins_destino'),
        ];
    }

    private function validar(int $idIns, int $idImpDestino, int $idPlanDestino): object
    {
        $origen = DB::table('t_inscripcion as ins')
            ->join('t_imparte as imp', 'imp.id_imp', '=', 'ins.id_imp')
            ->where('ins.id_ins', $idIns)
            ->first(['ins.id_ins', 'ins.id_us', 'ins.id_imp', 'ins.estado', 'imp.imparte_fecha_inicio']);

        if (! $origen) {
            throw new \DomainException('Inscripción no encontrada.');
        }
        if ((int) $origen->estado !== 1) {
            throw new \DomainException('Solo se pueden cambiar inscripciones activas.');
        }
        if ((int) $origen->id_imp === $idImpDestino) {
            throw new \DomainException('El estudiante ya está en esa versión del programa.');
        }

        $certificado = DB::table('t_certificado')
            ->where('usuario_id', $origen->id_us)
            ->where('imparte_id', $origen->id_imp)
            ->where('estado', '!=', 'anulado')
            ->value('codigo_verificacion');
        if ($certificado) {
            throw new \DomainException("No se puede cambiar de programa: ya tiene certificado emitido ({$certificado}). Anúlalo primero si corresponde.");
        }

        $destino = DB::table('t_imparte')->where('id_imp', $idImpDestino)->first(['id_imp', 'id_mat', 'estado']);
        if (! $destino || (int) $destino->estado !== 1) {
            throw new \DomainException('La versión de destino no existe o no está activa.');
        }

        $yaInscrito = DB::table('t_inscripcion')
            ->where('id_us', $origen->id_us)
            ->where('id_imp', $idImpDestino)
            ->where('estado', 1)
            ->exists();
        if ($yaInscrito) {
            throw new \DomainException('El estudiante ya tiene una inscripción activa en esa versión.');
        }

        $plan = DB::table('t_plan')->where('id_plan', $idPlanDestino)->first(['id_plan', 'estado']);
        $planDelDestino = DB::table('imparticion_planes')->where('id_imp', $idImpDestino)->where('id_plan', $idPlanDestino)->exists()
            || DB::table('programa_planes')->where('id_programa', $destino->id_mat)->where('id_plan', $idPlanDestino)->exists();
        if (! $plan || (int) $plan->estado !== 1 || ! $planDelDestino) {
            throw new \DomainException('Ese plan de pago no está habilitado para el programa de destino.');
        }

        return $origen;
    }

    /** @return array{0: array<int, array{nro: int, descripcion: string, monto: float, fecha_fin: ?string}>, 1: float} */
    private function cuotasDelPlan(int $idPlan): array
    {
        $cuotas = DB::table('t_fechapago')
            ->where('id_plan', $idPlan)
            ->where('estado', 1)
            ->orderBy('nro_pago')
            ->get(['nro_pago', 'tipo_tramite', 'monto_a_pagar', 'fecha_fin'])
            ->values()
            ->map(fn ($c, $i) => [
                'nro'         => (int) ($c->nro_pago ?: $i + 1),
                'descripcion' => $c->tipo_tramite ?: 'Cuota '.($c->nro_pago ?: $i + 1),
                'monto'       => (float) $c->monto_a_pagar,
                'fecha_fin'   => $c->fecha_fin,
            ])
            ->all();

        if ($cuotas === []) {
            $costo = (float) DB::table('t_plan')->where('id_plan', $idPlan)->value('costo');
            $cuotas = [['nro' => 1, 'descripcion' => 'Pago único', 'monto' => $costo, 'fecha_fin' => null]];
        }

        return [$cuotas, array_sum(array_column($cuotas, 'monto'))];
    }

    /** Marca como pagadas las cuotas cubiertas y deja la parcial con el saldo que falta. */
    /** @return int[] los mismos pagos que el detalle de la inscripción cuenta como suyos */
    private function idsPagosActivos(int $idIns): array
    {
        return collect($this->detalleHandler->handle(new GetInscripcionDetalleQuery($idIns))->pagos)
            ->map(fn ($p) => (array) $p)
            ->filter(fn ($p) => (int) ($p['pago_estado'] ?? 0) === 1)
            ->pluck('id_pago')
            ->filter()
            ->unique()
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }
}
