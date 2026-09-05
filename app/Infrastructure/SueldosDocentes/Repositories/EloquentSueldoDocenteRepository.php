<?php

namespace App\Infrastructure\SueldosDocentes\Repositories;

use App\Application\SueldosDocentes\DTOs\PagoSueldoDTO;
use App\Application\SueldosDocentes\DTOs\SueldoDocenteDTO;
use App\Domain\SueldosDocentes\Contracts\SueldoDocenteRepositoryInterface;
use App\Domain\SueldosDocentes\Exceptions\SueldoDocenteNotFoundException;
use App\Infrastructure\SueldosDocentes\Models\PagoSueldo;
use App\Infrastructure\SueldosDocentes\Models\SueldoDocente;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EloquentSueldoDocenteRepository implements SueldoDocenteRepositoryInterface
{
    private function baseQuery(): \Illuminate\Database\Query\Builder
    {
        return DB::table('web_sueldo_docente as s')
            ->leftJoin('t_usuario as u', function ($j) {

                $j->on('u.id_us', '=', 's.id_us')
                  ->where('s.id_us', '>', 0)
                  ->whereRaw("u.id_us_reg = COALESCE(
                        (SELECT MIN(u2.id_us_reg) FROM t_usuario u2 WHERE u2.id_us = u.id_us AND u2.tipoestudiante = '1'),
                        (SELECT MIN(u3.id_us_reg) FROM t_usuario u3 WHERE u3.id_us = u.id_us)
                    )");
            })
            ->leftJoin('web_docente_perfil as dp', function ($j) {
                $j->whereRaw('s.id_us < 0')
                  ->whereRaw('dp.id = ABS(s.id_us)');
            })
            ->leftJoin('t_imparte as imp', function ($j) {
                $j->on('imp.id_imp', '=', 's.id_imp')
                  ->whereRaw('imp.id_us_reg = (SELECT MIN(i2.id_us_reg) FROM t_imparte i2 WHERE i2.id_imp = imp.id_imp)');
            })
            ->leftJoin('t_materia as mat', function ($j) {
                $j->on('mat.id_mat', '=', 'imp.id_mat')
                  ->whereRaw('mat.id_us_reg = (SELECT MIN(m2.id_us_reg) FROM t_materia m2 WHERE m2.id_mat = mat.id_mat)');
            })
            ->leftJoin('t_programa as prog', 'prog.id_programa', '=', 's.id_programa')
            ->select([
                's.id', 's.id_us', 's.id_imp', 's.id_programa', 's.concepto', 's.periodo', 's.gestion',
                's.monto_total', 's.observacion', 's.archivo_pdf', 's.estado', 's.created_at',
                DB::raw("COALESCE(
                    NULLIF(TRIM(CONCAT(COALESCE(u.nombre,''), ' ', COALESCE(u.appaterno,''), ' ', COALESCE(u.apmaterno,''))), ''),
                    dp.nombre_completo
                ) as docente_nombre"),
                'u.ci as docente_ci',
                'u.celular as docente_celular',
                DB::raw("COALESCE(u.foto, dp.foto_url) as docente_foto"),
                DB::raw('COALESCE(imp.titulo_personalizado, mat.nombre, mat.nombremat, prog.nombre_programa) as nombre_curso'),
                DB::raw('COALESCE((
                    SELECT SUM(CAST(p.monto_pagado AS DECIMAL(12,2)))
                    FROM web_pago_sueldo p
                    WHERE p.id_sueldo = s.id AND p.estado = 1
                ), 0) as total_pagado'),
            ]);
    }

    public function paginate(PaginationDTO $pagination, array $filters = []): array
    {
        $q = $this->baseQuery()->where('s.estado', 1);

        if (! empty($filters['query'])) {
            $search = $filters['query'];
            $q->where(function ($sq) use ($search) {
                $sq->where('u.nombre', 'like', "%{$search}%")
                   ->orWhere('u.appaterno', 'like', "%{$search}%")
                   ->orWhere('s.concepto', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['periodo'])) {
            $q->where('s.periodo', $filters['periodo']);
        }

        if (! empty($filters['gestion'])) {
            $q->where('s.gestion', $filters['gestion']);
        }

        if (! empty($filters['estado_pago'])) {
            match ($filters['estado_pago']) {
                'pagado'    => $q->havingRaw('total_pagado >= s.monto_total AND s.monto_total > 0'),
                'parcial'   => $q->havingRaw('total_pagado > 0 AND total_pagado < s.monto_total'),
                'pendiente' => $q->havingRaw('total_pagado = 0'),
                default     => null,
            };
        }

        $total = DB::table(DB::raw("({$q->toSql()}) as sub"))
            ->mergeBindings($q)
            ->count();

        $data = $q->orderByDesc('s.created_at')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($r) => SueldoDocenteDTO::fromRow($r))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): SueldoDocenteDTO
    {
        $row = $this->baseQuery()->where('s.id', $id)->first();
        if (! $row) {
            throw new SueldoDocenteNotFoundException($id);
        }

        $pagos = PagoSueldo::where('id_sueldo', $id)
            ->where('estado', 1)
            ->orderByDesc('fecha_pago')
            ->get()
            ->map(fn ($p) => PagoSueldoDTO::fromModel($p))
            ->all();

        return SueldoDocenteDTO::fromRow($row, $pagos);
    }

    public function create(array $data): SueldoDocenteDTO
    {
        $model = SueldoDocente::create($data);
        return $this->findById($model->id);
    }

    public function update(int $id, array $data): SueldoDocenteDTO
    {
        $model = SueldoDocente::findOrFail($id);
        $model->update($data);
        return $this->findById($id);
    }

    public function delete(int $id): void
    {
        SueldoDocente::where('id', $id)->update(['estado' => 0]);
    }

    public function registrarPago(int $idSueldo, array $data): PagoSueldoDTO
    {
        $model = PagoSueldo::create(array_merge($data, ['id_sueldo' => $idSueldo]));
        return PagoSueldoDTO::fromModel($model);
    }

    public function registrarPagoLote(int $idSueldo, array $cuotas): array
    {
        return DB::transaction(function () use ($idSueldo, $cuotas) {
            $now    = now();
            $result = [];

            foreach ($cuotas as $cuota) {
                $model = PagoSueldo::create([
                    'id_sueldo'       => $idSueldo,
                    'monto_pagado'    => $cuota['monto_pagado'],
                    'fecha_pago'      => $cuota['fecha_pago'],
                    'nro_comprobante' => $cuota['nro_comprobante'] ?? null,
                    'observacion'     => $cuota['observacion'] ?? null,
                    'estado'          => 1,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ]);
                $result[] = PagoSueldoDTO::fromModel($model);
            }

            return $result;
        });
    }

    public function anularPago(int $idSueldo, int $pagoId): void
    {
        $pago = PagoSueldo::where('id', $pagoId)
            ->where('id_sueldo', $idSueldo)
            ->first();

        if ($pago && $pago->comprobante_archivo) {
            Storage::disk('public')->delete($pago->comprobante_archivo);
        }

        PagoSueldo::where('id', $pagoId)
            ->where('id_sueldo', $idSueldo)
            ->update(['estado' => 0]);
    }

    public function docentes(): array
    {

        return DB::table('web_docente_perfil as dp')
            ->select(
                DB::raw('COALESCE(dp.usuario_id, -dp.id) as id_us'),
                'dp.nombre_completo',
                DB::raw('NULL as ci'),
                DB::raw("CASE WHEN dp.usuario_id IS NOT NULL THEN 'legacy' ELSE 'perfil' END as fuente"),
            )
            ->where('dp.estado', 'publicado')
            ->orderBy('dp.nombre_completo')
            ->get()
            ->all();
    }

    public function imparticiones(): array
    {
        return DB::table('t_imparte as i')
            ->leftJoin('t_materia as m', function ($j) {
                $j->on('m.id_mat', '=', 'i.id_mat')
                  ->whereRaw('m.id_us_reg = (SELECT MIN(m2.id_us_reg) FROM t_materia m2 WHERE m2.id_mat = m.id_mat)');
            })
            ->select(
                'i.id_imp', 'i.periodo', 'i.gestion',
                DB::raw('COALESCE(i.titulo_personalizado, m.nombre, m.nombremat) as nombre_curso'),
            )
            ->where('i.estado', 1)
            ->orderByDesc('i.gestion')
            ->orderBy('i.periodo')
            ->get()
            ->all();
    }
}
