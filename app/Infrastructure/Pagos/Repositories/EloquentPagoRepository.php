<?php

namespace App\Infrastructure\Pagos\Repositories;

use App\Application\Pagos\DTOs\PagoDTO;
use App\Domain\Pagos\Contracts\PagoRepositoryInterface;
use App\Infrastructure\Pagos\Models\Pago;
use App\Infrastructure\Pagos\Models\PagoLog;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Support\Facades\DB;

class EloquentPagoRepository implements PagoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, array $filters = []): array
    {
        $q = Pago::query()
            ->leftJoin('t_usuario as u', function ($j) {
                $j->on('t_pago.id_us', '=', 'u.id_us')
                  ->whereRaw('u.id_us_reg = (SELECT MIN(u2.id_us_reg) FROM t_usuario u2 WHERE u2.id_us = u.id_us)');
            })
            ->leftJoin('tipos_banco as tb', 'tb.id', '=', 't_pago.tipo_banco_id')
            ->select([
                't_pago.*',
                DB::raw("TRIM(CONCAT(COALESCE(u.nombre,''), ' ', COALESCE(u.appaterno,''))) as estudiante_nombre"),
                'u.ci as estudiante_ci',
                'tb.nombre as tipo_banco_nombre',
            ]);

        if (! empty($filters['id_us'])) {
            $q->where('t_pago.id_us', (int) $filters['id_us']);
        }
        if (! empty($filters['id_fechapago'])) {
            $q->where('t_pago.id_fechapago', (int) $filters['id_fechapago']);
        }
        if (array_key_exists('idImpPermitidos', $filters) && $filters['idImpPermitidos'] !== null) {

            $q->whereIn('t_pago.id_ins', function ($sub) use ($filters) {
                $sub->select('id_ins')->from('t_inscripcion')->whereIn('id_imp', $filters['idImpPermitidos']);
            });
        }
        if (isset($filters['conInactivos']) && ! $filters['conInactivos']) {
            $q->where('t_pago.estado', 1);
        } elseif (! isset($filters['conInactivos'])) {
            $q->where('t_pago.estado', 1);
        }

        $total = $q->count();
        $data  = $q->orderByDesc('t_pago.fecha_deposito')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn($m) => PagoDTO::fromModel($m))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): mixed
    {
        return Pago::find($id);
    }

    public function existePagoActivo(int $idUs, int $idFechapago): bool
    {
        return Pago::where('id_us', $idUs)
            ->where('id_fechapago', $idFechapago)
            ->where('estado', 1)
            ->exists();
    }

    public function existePorBoleta(string $nroBoleta): bool
    {
        return Pago::where('nro_boleta_bancaria', $nroBoleta)
            ->where('estado', 1)
            ->exists();
    }

    public function existePagoActivoConLock(int $idUs, int $idFechapago): bool
    {
        return Pago::where('id_us', $idUs)
            ->where('id_fechapago', $idFechapago)
            ->where('estado', 1)
            ->lockForUpdate()
            ->exists();
    }

    public function existePorBoletaConLock(string $nroBoleta): bool
    {
        return Pago::where('nro_boleta_bancaria', $nroBoleta)
            ->where('estado', 1)
            ->lockForUpdate()
            ->exists();
    }

    public function create(array $data): mixed
    {
        return Pago::create($data);
    }

    public function update(int $id, array $data): mixed
    {
        $pago = Pago::findOrFail($id);
        $pago->update($data);
        return $pago->fresh();
    }

    public function anular(int $id, int $idUsReg): mixed
    {
        $pago = Pago::findOrFail($id);
        $pago->update(['estado' => 0]);
        return $pago->fresh();
    }

    public function auditarCambio(object $pago, string $tipoLog, int $idUsReg): void
    {
        PagoLog::create([
            'id_pago'             => $pago->id_pago,
            'tipo_log'            => $tipoLog,
            'id_us_reg'           => $idUsReg,
            'num_pago'            => $pago->num_pago ?? 0,
            'id_us'               => $pago->id_us,
            'id_mat'              => $pago->id_mat ?? null,
            'id_fechapago'        => $pago->id_fechapago ?? null,
            'monto_pagado'        => $pago->monto_pagado,
            'monto_pago_extra'    => $pago->monto_pago_extra ?? null,
            'nro_boleta_bancaria' => $pago->nro_boleta_bancaria ?? null,
            'fecha_deposito'      => $pago->fecha_deposito ?? null,
            'nro_nit'             => $pago->nro_nit ?? null,
            'nombre_nit'          => $pago->nombre_nit ?? null,
            'tipo_fechapago'      => $pago->tipo_fechapago ?? null,
            'observacion_pago'    => $pago->observacion_pago ?? null,
            'pago_extra'          => $pago->pago_extra ?? 0,
            'estado'              => $pago->estado,
            'fecha_reg'           => now(),
            'monto_descuento_extra' => $pago->monto_descuento_extra ?? null,
            'motivo_descuento'      => $pago->motivo_descuento ?? null,
        ]);
    }

    public function registrarLogPagoOnlineFallido(string $referencia, int $idUs, float $monto, string $detalle): void
    {
        PagoLog::create([
            'id_pago'             => 0,
            'tipo_log'            => 'pago_online_fallido',
            'id_us_reg'           => 0,
            'num_pago'            => 0,
            'id_us'               => $idUs,
            'monto_pagado'        => $monto,
            'nro_boleta_bancaria' => $referencia,
            'observacion_pago'    => $detalle,
            'estado'              => 0,
            'fecha_reg'           => now(),
        ]);
    }
}
