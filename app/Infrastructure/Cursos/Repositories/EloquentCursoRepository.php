<?php

namespace App\Infrastructure\Cursos\Repositories;

use App\Application\Cursos\DTOs\CursoDTO;
use App\Application\Cursos\DTOs\CuotaDTO;
use App\Domain\Cursos\Contracts\CursoRepositoryInterface;
use App\Domain\Cursos\Exceptions\CursoConInscritosException;
use App\Domain\Cursos\Exceptions\CursoNotFoundException;
use App\Infrastructure\Cursos\Models\Curso;
use App\Shared\Kernel\DTOs\PaginationDTO;
use App\Shared\Kernel\Support\SqlCompat;
use Illuminate\Support\Facades\DB;

class EloquentCursoRepository implements CursoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloPublicados = false, ?array $idsPermitidos = null): array
    {
        $planSub = DB::table('t_plan')
            ->select(
                'id_plan',
                DB::raw('MIN(titulo_plan) as plan_titulo'),
                DB::raw('MIN(titulo) as plan_titulo_alt'),
                DB::raw('MIN(nro_cuotas) as plan_nro_cuotas'),
                DB::raw('MIN(costo) as plan_costo'),
                DB::raw('MIN(costo_por_cuota) as plan_costo_cuota'),
            )
            ->groupBy('id_plan');

        
        
        
        
        $inscritosSub = DB::table('t_inscripcion')
            ->selectRaw('id_imp, COUNT(DISTINCT id_ins) as total_inscritos')
            ->groupBy('id_imp');

        
        
        
        $recaudadoSub = DB::table('t_inscripcion as i')
            ->join('t_pago as pg', 'pg.id_ins', '=', 'i.id_ins')
            ->where('pg.estado', 1)
            ->selectRaw('i.id_imp, COALESCE(SUM(CAST(pg.monto_pagado AS DECIMAL(12,2))), 0) as total_recaudado')
            ->groupBy('i.id_imp');

        $q = DB::table('t_programa as p')
            ->leftJoin('web_categoria_programa as cat', 'p.categoria_web_id', '=', 'cat.id')
            ->leftJoin('web_categoria_programa as tip', 'p.id_tipoprograma', '=', 'tip.tipo_programa_id')
            ->leftJoin('web_area as area', 'p.area_id', '=', 'area.id')
            ->leftJoinSub($planSub, 'pl', 'pl.id_plan', '=', 'p.id_plan')
            ->leftJoinSub($inscritosSub, 'insc', 'insc.id_imp', '=', 'p.id_imp')
            ->leftJoinSub($recaudadoSub, 'rec', 'rec.id_imp', '=', 'p.id_imp')
            ->leftJoin('web_config_honorario_programa as ch', 'ch.id_programa', '=', 'p.id_programa')
            ->select(
                'p.id_programa', 'p.id_us_reg', 'p.nombre_programa', 'p.slug',
                'p.descripcion', 'p.objetivo', 'p.dirigido', 'p.requisitos',
                'p.inversion', 'p.costo_monto', 'p.creditaje', 'p.nota', 'p.url_video',
                'p.foto', 'p.imagen_banner_url', 'p.imagen_alt',
                'p.inicio_actividades', 'p.finalizacion_actividades', 'p.inicio_inscripciones', 'p.mes_facturacion',
                'ch.tipo_honorario',
                'p.id_tipoprograma', 'tip.nombre as tipo_nombre',
                'p.categoria_web_id', 'cat.nombre as categoria_nombre',
                'p.formulario_id',
                'p.area_id', 'area.titulo as area_titulo', 'area.slug as area_slug',
                'area.logo_url as area_logo_url', 'area.color as area_color', 'area.icono as area_icono',
                'p.estado', 'p.estado_web', 'p.destacado', 'p.orden',
                'p.meta_titulo', 'p.meta_descripcion', 'p.fecha_publicacion', 'p.fecha_reg',
                'p.url_whatsapp', 'p.url_whatsapp2', 'p.id_plan', 'p.id_plandoc', 'p.id_imp', 'p.convenio_id',
                'pl.plan_titulo', 'pl.plan_titulo_alt', 'pl.plan_nro_cuotas', 'pl.plan_costo', 'pl.plan_costo_cuota',
                DB::raw('COALESCE(insc.total_inscritos, 0) as total_inscritos'),
                DB::raw('COALESCE(rec.total_recaudado, 0) as total_recaudado'),
            );

        if ($pagination->query) {
            $q->where(function ($sub) use ($pagination) {
                $sub->where('p.nombre_programa', 'like', "%{$pagination->query}%")
                    ->orWhere('p.descripcion', 'like', "%{$pagination->query}%");
            });
        }

        if ($soloPublicados) {
            
            
            $q->where('p.estado_web', 'publicado')->where('p.estado', 1);
        }

        if ($idsPermitidos !== null) {
            $q->whereIn('p.id_programa', $idsPermitidos);
        }

        $total = $q->count();
        $items = (clone $q)
            ->orderBy('p.orden', 'asc')
            ->orderBy('p.id_programa', 'desc')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get();

        return [
            'data'  => $items->map(fn ($row) => CursoDTO::fromModel($row))->all(),
            'total' => $total,
        ];
    }

    public function findById(int $id, bool $soloPublicados = false): CursoDTO
    {
        $q = $this->queryConPlan()->where('p.id_programa', $id);
        if ($soloPublicados) {
            $q->where('p.estado_web', 'publicado')->where('p.estado', 1);
        }
        $row = $q->first();
        if (! $row) {
            throw new CursoNotFoundException($id);
        }

        return $this->buildDtoConCuotas($row);
    }

    public function findBySlug(string $slug, bool $soloPublicados = false): CursoDTO
    {
        $q = $this->queryConPlan()->where('p.slug', $slug);
        if ($soloPublicados) {
            
            
            $q->where('p.estado_web', 'publicado')->where('p.estado', 1);
        }
        $row = $q->first();
        if (! $row) {
            throw new CursoNotFoundException($slug);
        }

        return $this->buildDtoConCuotas($row);
    }

    private function queryConPlan(): \Illuminate\Database\Query\Builder
    {
        return DB::table('t_programa as p')
            ->leftJoin('t_plan as pl', 'pl.id_plan', '=', 'p.id_plan')
            ->leftJoin('web_categoria_programa as cat', 'p.categoria_web_id', '=', 'cat.id')
            ->leftJoin('web_categoria_programa as tip', 'p.id_tipoprograma', '=', 'tip.tipo_programa_id')
            ->leftJoin('web_area as area', 'p.area_id', '=', 'area.id')
            ->leftJoin('vendedores as vend', 'vend.id', '=', 'p.vendedor_id')
            ->leftJoin('web_config_honorario_programa as ch', 'ch.id_programa', '=', 'p.id_programa')
            ->select(
                'p.*',
                'ch.tipo_honorario',
                'pl.titulo_plan as plan_titulo',
                'pl.titulo as plan_titulo_alt',
                DB::raw(SqlCompat::castDecimal('pl.costo') . ' as plan_costo'),
                DB::raw(SqlCompat::castUnsignedInt('pl.nro_cuotas') . ' as plan_nro_cuotas'),
                DB::raw(SqlCompat::castDecimal('pl.costo_por_cuota') . ' as plan_costo_cuota'),
                'tip.nombre as tipo_nombre',
                'cat.nombre as categoria_nombre',
                'area.titulo as area_titulo', 'area.slug as area_slug',
                'area.logo_url as area_logo_url', 'area.color as area_color', 'area.icono as area_icono',
                DB::raw(SqlCompat::trimConcat("COALESCE(vend.nombre,'')", "' '", "COALESCE(vend.apellido,'')") . ' as vendedor_nombre'),
            );
    }

    private function buildDtoConCuotas(object $row): CursoDTO
    {
        $tareas_catalogo_ids = DB::table('tareas_academicas')
            ->where('id_programa', $row->id_programa)
            ->whereNotNull('catalogo_id')
            ->pluck('catalogo_id')
            ->all();

        $row->tareas_catalogo_ids = $tareas_catalogo_ids;
        $dto = CursoDTO::fromModel($row);

        if (! $row->id_plan) {
            return $dto;
        }

        $cuotas = DB::table('t_fechapago')
            ->where('id_plan', $row->id_plan)
            ->where('estado', 1)
            ->orderByRaw(SqlCompat::castUnsignedInt('num_fechapago') . ' ASC')
            ->select('id_fechapago', 'nro_pago', 'monto_a_pagar', 'tipo_tramite', 'fecha_inicio', 'fecha_fin', 'obligatorio')
            ->get()
            ->map(fn ($r) => CuotaDTO::fromRow($r))
            ->all();

        return $dto->withCuotas($cuotas);
    }

    public function create(array $data): CursoDTO
    {
        $model = DB::transaction(function () use ($data) {
            
            
            
            
            
            
            $ultimoPrograma = DB::table('t_programa')
                ->orderByDesc('id_programa')
                ->lockForUpdate()
                ->first();
            $nextId = ($ultimoPrograma->id_programa ?? 0) + 1;
            $data['id_programa'] = $nextId;
            $data['id_us_reg']   = $data['id_us_reg'] ?? 0;
            $data['estado']      = $data['estado'] ?? 1;
            $data['fecha_reg']   = now()->toDateTimeString();
            $data['updated_at']  = now()->toDateTimeString();

            Curso::create($data);

            return Curso::find($nextId);
        });

        return CursoDTO::fromModel($model);
    }

    public function update(int $id, array $data): CursoDTO
    {
        $model = Curso::find($id);
        if (! $model) {
            throw new CursoNotFoundException($id);
        }

        $data['updated_at'] = now()->toDateTimeString();
        $model->update($data);

        return CursoDTO::fromModel($model);
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $curso = Curso::where('id_programa', $id)->lockForUpdate()->first();
            if (! $curso) {
                return false;
            }

            if ($curso->id_imp) {
                DB::table('t_imparte')->where('id_imp', $curso->id_imp)->lockForUpdate()->first();

                if (DB::table('t_inscripcion')->where('id_imp', $curso->id_imp)->exists()) {
                    throw new CursoConInscritosException();
                }
            }

            DB::table('web_programa_docente')->where('programa_id', $id)->delete();
            DB::table('web_config_honorario_programa')->where('id_programa', $id)->delete();

            return Curso::where('id_programa', $id)->delete() > 0;
        });
    }

    public function tieneInscritos(int $id): bool
    {
        $idImp = Curso::where('id_programa', $id)->value('id_imp');

        if (! $idImp) {
            return false;
        }

        return DB::table('t_inscripcion')->where('id_imp', $idImp)->exists();
    }

    public function docentesIndex(int $programaId, bool $soloVisibles = false): array
    {
        if (! Curso::where('id_programa', $programaId)->exists()) {
            throw new CursoNotFoundException($programaId);
        }

        $q = DB::table('web_programa_docente as pd')
            ->join('web_docente_perfil as dp', 'pd.docente_id', '=', 'dp.id')
            ->where('pd.programa_id', $programaId);

        if ($soloVisibles) {
            $q->where('dp.estado', 'publicado')->where('dp.mostrar_en_web', true);
        }

        return $q->orderBy('pd.orden')
            ->orderBy('dp.nombre_completo')
            ->select('dp.*', 'pd.orden as pivot_orden')
            ->get()
            ->all();
    }

    public function docentesByImp(int $idImp, bool $soloVisibles = false): array
    {
        $programaId = DB::table('t_programa')
            ->where('id_imp', $idImp)
            ->value('id_programa');

        if (! $programaId) {
            return [];
        }

        return $this->docentesIndex((int) $programaId, $soloVisibles);
    }

    public function docentesAttach(int $programaId, int $docenteId, int $orden): void
    {
        if (! Curso::where('id_programa', $programaId)->exists()) {
            throw new CursoNotFoundException($programaId);
        }

        DB::table('web_programa_docente')->insertOrIgnore([
            'programa_id' => $programaId,
            'docente_id'  => $docenteId,
            'orden'       => $orden,
        ]);
    }

    public function docentesDetach(int $programaId, int $docenteId): void
    {
        DB::table('web_programa_docente')
            ->where('programa_id', $programaId)
            ->where('docente_id', $docenteId)
            ->delete();
    }

    public function estadisticasGlobales(string $fechaInicio, string $fechaFin, ?array $idImpPermitidos = null): array
    {
        
        
        
        
        
        
        
        
        $fechaEfectiva = "COALESCE(NULLIF(fecha_ins, '2000-01-01'), CAST(fecha_reg AS DATE))";

        $inscritos = (int) DB::table('t_inscripcion')
            ->whereRaw("{$fechaEfectiva} BETWEEN ? AND ?", [$fechaInicio, $fechaFin])
            ->when($idImpPermitidos !== null, fn ($q) => $q->whereIn('id_imp', $idImpPermitidos))
            ->distinct()
            ->count('id_ins');

        
        
        
        
        
        
        
        $ingresosQuery = DB::table('t_pago')
            ->where('estado', 1)
            ->whereBetween('fecha_deposito', [$fechaInicio, $fechaFin]);

        if ($idImpPermitidos !== null) {
            $ingresosQuery->whereIn('id_ins', function ($sub) use ($idImpPermitidos) {
                $sub->select('id_ins')->from('t_inscripcion')->whereIn('id_imp', $idImpPermitidos);
            });
        }

        $ingresos = (float) $ingresosQuery
            ->selectRaw('COALESCE(SUM(CAST(monto_pagado AS DECIMAL(12,2))), 0) as total')
            ->value('total');

        return ['inscritos' => $inscritos, 'ingresos' => $ingresos];
    }

    public function inscritosPorPeriodo(string $fechaInicio, string $fechaFin, ?array $idImpPermitidos = null): \Illuminate\Support\Collection
    {
        $fechaEfectiva = "COALESCE(NULLIF(ins.fecha_ins, '2000-01-01'), CAST(ins.fecha_reg AS DATE))";

        return DB::table('t_inscripcion as ins')
            
            
            
            ->whereRaw('ins.id_us_reg = (SELECT MIN(i2.id_us_reg) FROM t_inscripcion i2 WHERE i2.id_ins = ins.id_ins)')
            ->leftJoin('t_usuario as u', function ($j) {
                $j->on('ins.id_us', '=', 'u.id_us')
                  ->whereRaw("u.id_us_reg = COALESCE(
                        (SELECT MIN(u2.id_us_reg) FROM t_usuario u2 WHERE u2.id_us = u.id_us AND u2.tipoestudiante = '2'),
                        (SELECT MIN(u3.id_us_reg) FROM t_usuario u3 WHERE u3.id_us = u.id_us)
                    )");
            })
            ->leftJoin('t_imparte as imp', 'ins.id_imp', '=', 'imp.id_imp')
            ->leftJoin('t_materia as m', function ($j) {
                $j->on('imp.id_mat', '=', 'm.id_mat')
                  ->whereRaw('m.id_us_reg = (SELECT MIN(m2.id_us_reg) FROM t_materia m2 WHERE m2.id_mat = m.id_mat)');
            })
            ->whereRaw("{$fechaEfectiva} BETWEEN ? AND ?", [$fechaInicio, $fechaFin])
            ->when($idImpPermitidos !== null, fn ($q) => $q->whereIn('ins.id_imp', $idImpPermitidos))
            ->selectRaw("
                ins.id_ins,
                {$fechaEfectiva} as fecha_ins_efectiva,
                ins.estado,
                COALESCE(ins.canal_venta, 'admin') as canal_venta,
                " . SqlCompat::trimConcat("COALESCE(u.nombre,'')", "' '", "COALESCE(u.appaterno,'')") . " as estudiante_nombre,
                u.ci as estudiante_ci,
                COALESCE((SELECT p2.nombre_programa FROM t_programa p2 WHERE p2.id_imp = imp.id_imp ORDER BY p2.id_us_reg LIMIT 1), m.nombremat) as curso_nombre
            ")
            ->orderBy('fecha_ins_efectiva')
            ->get();
    }

    public function pagosPorPeriodo(string $fechaInicio, string $fechaFin, ?array $idImpPermitidos = null): \Illuminate\Support\Collection
    {
        return DB::table('t_pago as p')
            ->leftJoin('t_usuario as u', function ($j) {
                $j->on('p.id_us', '=', 'u.id_us')
                  ->whereRaw("u.id_us_reg = COALESCE(
                        (SELECT MIN(u2.id_us_reg) FROM t_usuario u2 WHERE u2.id_us = u.id_us AND u2.tipoestudiante = '2'),
                        (SELECT MIN(u3.id_us_reg) FROM t_usuario u3 WHERE u3.id_us = u.id_us)
                    )");
            })
            ->leftJoin('t_inscripcion as ins', 'p.id_ins', '=', 'ins.id_ins')
            ->leftJoin('t_imparte as imp', 'ins.id_imp', '=', 'imp.id_imp')
            ->leftJoin('t_materia as m', function ($j) {
                $j->on('imp.id_mat', '=', 'm.id_mat')
                  ->whereRaw('m.id_us_reg = (SELECT MIN(m2.id_us_reg) FROM t_materia m2 WHERE m2.id_mat = m.id_mat)');
            })
            ->where('p.estado', 1)
            ->whereBetween('p.fecha_deposito', [$fechaInicio, $fechaFin])
            ->when($idImpPermitidos !== null, fn ($q) => $q->whereIn('ins.id_imp', $idImpPermitidos))
            ->selectRaw("
                p.id_pago,
                p.fecha_deposito,
                " . SqlCompat::castDecimal('p.monto_pagado') . " as monto_pagado,
                COALESCE(p.metodo_pago, 'efectivo') as metodo_pago,
                " . SqlCompat::trimConcat("COALESCE(u.nombre,'')", "' '", "COALESCE(u.appaterno,'')") . " as estudiante_nombre,
                u.ci as estudiante_ci,
                COALESCE((SELECT p2.nombre_programa FROM t_programa p2 WHERE p2.id_imp = imp.id_imp ORDER BY p2.id_us_reg LIMIT 1), m.nombremat) as curso_nombre
            ")
            ->orderBy('p.fecha_deposito')
            ->get();
    }

    public function estudiantesParaExportMoodle(int $idImp): array
    {
        return DB::table('t_inscripcion as ins')
            
            
            ->whereRaw('ins.id_us_reg = (SELECT MIN(i2.id_us_reg) FROM t_inscripcion i2 WHERE i2.id_ins = ins.id_ins)')
            ->leftJoin('t_usuario as u', function ($j) {
                $j->on('ins.id_us', '=', 'u.id_us')
                  ->whereRaw("u.id_us_reg = COALESCE(
                        (SELECT MIN(u2.id_us_reg) FROM t_usuario u2 WHERE u2.id_us = u.id_us AND u2.tipoestudiante = '2'),
                        (SELECT MIN(u3.id_us_reg) FROM t_usuario u3 WHERE u3.id_us = u.id_us)
                    )");
            })
            ->where('ins.id_imp', $idImp)
            ->where('ins.estado', 1)
            ->select([
                'u.id_us', 'u.nombre_usuario', 'u.nombre', 'u.appaterno', 'u.apmaterno', 'u.email', 'u.ci',
            ])
            ->orderBy('u.appaterno')
            ->get()
            ->all();
    }

    public function idMatDeImparticion(int $idImp): ?int
    {
        $idMat = DB::table('t_imparte')->where('id_imp', $idImp)->value('id_mat');

        return $idMat ? (int) $idMat : null;
    }

    public function planVinculadoAMateria(int $idMat): ?int
    {
        $idPlan = DB::table('t_materia_plan')
            ->where('id_mat', $idMat)
            ->where('estado', 1)
            ->orderByDesc('id_matplan')
            ->value('id_plan');

        return $idPlan ? (int) $idPlan : null;
    }

    public function vincularPlanAMateria(int $idMat, int $idPlan, int $idUsReg): void
    {
        $idMatplan = time();
        while (DB::table('t_materia_plan')->where('id_matplan', $idMatplan)->exists()) {
            $idMatplan++;
        }

        DB::table('t_materia_plan')->insert([
            'id_matplan'         => $idMatplan,
            'id_us_reg'          => $idUsReg,
            'num_mp'             => 0,
            'id_mat'             => $idMat,
            'id_plan'            => $idPlan,
            'carga_horaria_plan' => '0',
            'id_preesp'          => 1,
            'fecha_reg'          => now(),
            'estado'             => 1,
            'per_modificar'      => 0,
        ]);
    }

    public function alertasCobrosPorImparticion(int $diasProximos, ?array $idImpPermitidos = null): array
    {
        $hoy    = now()->format('Y-m-d');
        $limite = now()->addDays($diasProximos)->format('Y-m-d');

        $rows = DB::table('t_fechapago as fp')
            ->join('t_inscripcion as ins', 'ins.id_plan', '=', 'fp.id_plan')
            ->leftJoin('t_pago as p', function ($j) {
                $j->on('p.id_fechapago', '=', 'fp.id_fechapago')
                  ->where('p.estado', 1);
            })
            ->where('fp.estado', 1)
            ->where('ins.estado', 1)
            ->whereNull('p.id_pago')
            ->where('fp.fecha_fin', '<=', $limite)
            ->when($idImpPermitidos !== null, fn ($q) => $q->whereIn('ins.id_imp', $idImpPermitidos))
            ->selectRaw('
                ins.id_imp,
                SUM(CASE WHEN fp.fecha_fin >= ? THEN 1 ELSE 0 END) as proximas,
                SUM(CASE WHEN fp.fecha_fin < ? THEN 1 ELSE 0 END) as vencidas
            ', [$hoy, $hoy])
            ->groupBy('ins.id_imp')
            ->get();

        return $rows->map(fn ($r) => [
            'id_imp'   => (int) $r->id_imp,
            'proximas' => (int) $r->proximas,
            'vencidas' => (int) $r->vencidas,
        ])->all();
    }
}
