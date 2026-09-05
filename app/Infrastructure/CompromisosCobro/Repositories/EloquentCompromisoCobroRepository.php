<?php

namespace App\Infrastructure\CompromisosCobro\Repositories;

use App\Application\CompromisosCobro\DTOs\CompromisoCobroDTO;
use App\Application\CompromisosCobro\DTOs\CompromisoCobroLogDTO;
use App\Domain\CompromisosCobro\Contracts\CompromisoCobroRepositoryInterface;
use App\Domain\CompromisosCobro\Exceptions\CompromisoCobroNotFoundException;
use App\Infrastructure\CompromisosCobro\Models\CompromisoCobro;
use App\Infrastructure\CompromisosCobro\Models\CompromisoCobroLog;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentCompromisoCobroRepository implements CompromisoCobroRepositoryInterface
{
    private function baseQuery(): \Illuminate\Database\Query\Builder
    {
        return DB::table('web_compromiso_cobro as cc')
            ->leftJoin('t_usuario as u', function ($j) {
                $j->on('cc.id_us', '=', 'u.id_us')
                  ->whereRaw("u.id_us_reg = COALESCE(
                        (SELECT MIN(u2.id_us_reg) FROM t_usuario u2 WHERE u2.id_us = u.id_us AND u2.tipoestudiante = '2'),
                        (SELECT MIN(u3.id_us_reg) FROM t_usuario u3 WHERE u3.id_us = u.id_us)
                    )");
            })
            ->leftJoin('usuarios as reg', 'reg.id', '=', 'cc.registrado_por')
            ->select([
                'cc.*',
                DB::raw("TRIM(CONCAT(COALESCE(u.nombre,''), ' ', COALESCE(u.appaterno,''))) as estudiante_nombre"),
                'u.ci as estudiante_ci',
                DB::raw("(SELECT p2.nombre_programa FROM t_programa p2 WHERE p2.id_imp = cc.id_imp ORDER BY p2.id_us_reg LIMIT 1) as curso_nombre"),
                DB::raw("NULLIF(TRIM(CONCAT(COALESCE(reg.nombre,''), ' ', COALESCE(reg.apellido,''))), '') as registrado_por_nombre"),
            ]);
    }

    public function create(array $data): CompromisoCobroDTO
    {
        $model = CompromisoCobro::create($data);

        return CompromisoCobroDTO::fromModel($this->baseQuery()->where('cc.id', $model->id)->first());
    }

    public function update(int $id, array $data): CompromisoCobroDTO
    {
        $model = CompromisoCobro::find($id);
        if (! $model) {
            throw new CompromisoCobroNotFoundException($id);
        }

        $model->update($data);

        return CompromisoCobroDTO::fromModel($this->baseQuery()->where('cc.id', $id)->first());
    }

    public function findById(int $id): CompromisoCobroDTO
    {
        $row = $this->baseQuery()->where('cc.id', $id)->first();
        if (! $row) {
            throw new CompromisoCobroNotFoundException($id);
        }

        return CompromisoCobroDTO::fromModel($row);
    }

    public function findAbiertoPorInscripcion(int $idIns): ?CompromisoCobroDTO
    {
        $row = $this->baseQuery()
            ->where('cc.id_ins', $idIns)
            ->whereIn('cc.estado', ['pendiente', 'incumplido'])
            ->orderByDesc('cc.id')
            ->first();

        return $row ? CompromisoCobroDTO::fromModel($row) : null;
    }

    public function existeAbiertoPara(int $idIns): bool
    {
        return DB::table('web_compromiso_cobro')
            ->where('id_ins', $idIns)
            ->whereIn('estado', ['pendiente', 'incumplido'])
            ->exists();
    }

    public function existeAbiertoParaConLock(int $idIns): bool
    {
        return DB::table('web_compromiso_cobro')
            ->where('id_ins', $idIns)
            ->whereIn('estado', ['pendiente', 'incumplido'])
            ->lockForUpdate()
            ->exists();
    }

    public function registrarLog(int $compromisoCobroId, array $data): void
    {
        CompromisoCobroLog::create(array_merge(['compromiso_cobro_id' => $compromisoCobroId], $data));
    }

    public function logsDe(int $compromisoCobroId): Collection
    {
        return DB::table('web_compromiso_cobro_log as l')
            ->leftJoin('usuarios as reg', 'reg.id', '=', 'l.registrado_por')
            ->select([
                'l.*',
                DB::raw("NULLIF(TRIM(CONCAT(COALESCE(reg.nombre,''), ' ', COALESCE(reg.apellido,''))), '') as registrado_por_nombre"),
            ])
            ->where('l.compromiso_cobro_id', $compromisoCobroId)
            ->orderByDesc('l.id')
            ->get()
            ->map(fn ($row) => CompromisoCobroLogDTO::fromModel($row));
    }

    public function paginate(PaginationDTO $pagination, ?string $estado, ?array $idImpPermitidos): array
    {
        $q = $this->baseQuery();

        if ($estado !== null) {
            $q->where('cc.estado', $estado);
        }

        if ($idImpPermitidos !== null) {
            $q->whereIn('cc.id_imp', $idImpPermitidos);
        }

        if ($pagination->query) {
            $search = $pagination->query;
            $q->where(function ($sq) use ($search) {
                $sq->where('u.nombre', 'like', "%{$search}%")
                   ->orWhere('u.appaterno', 'like', "%{$search}%")
                   ->orWhere('u.ci', 'like', "%{$search}%");
            });
        }

        $total = $q->count();
        $items = $q->orderByRaw("CASE WHEN cc.estado IN ('pendiente','incumplido') THEN 0 ELSE 1 END")
            ->orderBy('cc.fecha_compromiso')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get();

        return [
            'data'  => $items->map(fn ($row) => CompromisoCobroDTO::fromModel($row))->all(),
            'total' => $total,
        ];
    }

    public function resumen(?array $idImpPermitidos): array
    {
        $q = DB::table('web_compromiso_cobro')->whereIn('estado', ['pendiente', 'incumplido']);

        if ($idImpPermitidos !== null) {
            $q->whereIn('id_imp', $idImpPermitidos);
        }

        $vencidos = (clone $q)->whereDate('fecha_compromiso', '<', now()->toDateString())->count();
        $hoy      = (clone $q)->whereDate('fecha_compromiso', '=', now()->toDateString())->count();

        return ['vencidos' => $vencidos, 'hoy' => $hoy];
    }

    public function pendientesConFechaHoySinNotificar(): Collection
    {

        return $this->baseQuery()
            ->where('cc.estado', 'pendiente')
            ->whereDate('cc.fecha_compromiso', '=', now()->toDateString())
            ->whereRaw("(cc.fecha_compromiso + COALESCE(cc.hora_compromiso, TIME '08:00:00')) <= ?", [now()])
            ->whereNull('cc.notificado_at')
            ->get()
            ->map(fn ($row) => CompromisoCobroDTO::fromModel($row));
    }

    public function pendientesVencidosSinNotificar(): Collection
    {
        return $this->baseQuery()
            ->whereIn('cc.estado', ['pendiente', 'incumplido'])
            ->whereDate('cc.fecha_compromiso', '<', now()->toDateString())
            ->whereNull('cc.vencido_notificado_at')
            ->get()
            ->map(fn ($row) => CompromisoCobroDTO::fromModel($row));
    }

    public function marcarCumplidosDe(int $idIns, int $registradoPor): void
    {

        DB::transaction(function () use ($idIns, $registradoPor) {
            $abiertos = CompromisoCobro::where('id_ins', $idIns)
                ->whereIn('estado', ['pendiente', 'incumplido'])
                ->get();

            foreach ($abiertos as $compromiso) {
                CompromisoCobroLog::create([
                    'compromiso_cobro_id' => $compromiso->id,
                    'tipo_evento'         => 'cumplido',
                    'observacion'         => 'Cerrado automáticamente al registrarse un pago.',
                    'registrado_por'      => $registradoPor,
                ]);

                $compromiso->update(['estado' => 'cumplido']);
            }
        });
    }
}
