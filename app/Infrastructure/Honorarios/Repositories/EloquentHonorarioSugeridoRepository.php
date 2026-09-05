<?php

namespace App\Infrastructure\Honorarios\Repositories;

use App\Application\Honorarios\DTOs\DocenteHonorarioSugeridoDTO;
use App\Application\Honorarios\Services\HonorarioCalculadorService;
use App\Domain\Honorarios\Contracts\HonorarioSugeridoRepositoryInterface;
use Illuminate\Support\Facades\DB;

class EloquentHonorarioSugeridoRepository implements HonorarioSugeridoRepositoryInterface
{
    public function __construct(private readonly HonorarioCalculadorService $calculador) {}

    public function docentesActivosDelMesConSugerido(int $anio, int $mes): array
    {
        $inicioMes      = sprintf('%04d-%02d-01', $anio, $mes);
        $finMes         = date('Y-m-t', strtotime($inicioMes));
        $mesFacturacion = sprintf('%04d-%02d', $anio, $mes);

        $filas = DB::table('t_programa as prog')
            ->join('web_programa_docente as pd', 'pd.programa_id', '=', 'prog.id_programa')
            ->join('web_docente_perfil as dp', function ($j) {
                $j->on('dp.id', '=', 'pd.docente_id')
                  ->where('dp.estado', 'publicado');
            })
            ->leftJoin('t_imparte as imp', 'imp.id_imp', '=', 'prog.id_imp')
            ->leftJoin('t_materia as mat', function ($j) {
                $j->on('mat.id_mat', '=', 'imp.id_mat')
                  ->whereRaw('mat.id_us_reg = (SELECT MIN(m2.id_us_reg) FROM t_materia m2 WHERE m2.id_mat = mat.id_mat)');
            })
            ->join('web_config_honorario_programa as cfg', 'cfg.id_programa', '=', 'prog.id_programa')
            ->select([
                'prog.id_programa',
                'prog.nombre_programa',
                'prog.id_imp',
                DB::raw('COALESCE(dp.usuario_id, -dp.id) as id_us'),
                'dp.nombre_completo as docente_nombre',
                DB::raw("COALESCE(mat.nombremat, mat.nombre, prog.nombre_programa) as nombre_curso"),
                'cfg.tipo_honorario',
                'cfg.monto_fijo',
                'cfg.monto_por_dia',
            ])
            ->where('prog.estado', 1)

            ->where(function ($q) use ($mesFacturacion) {
                $q->whereNull('prog.mes_facturacion')
                  ->orWhere('prog.mes_facturacion', $mesFacturacion);
            })
            ->get();

        return $filas->map(function ($row) use ($anio, $mes, $inicioMes, $finMes) {
            $diasSemana = $row->id_imp
                ? DB::table('t_horario')
                    ->where('id_imp', $row->id_imp)
                    ->where('estado', 1)
                    ->pluck('id_d')
                    ->filter(fn ($d) => $d !== null)
                    ->unique()
                    ->values()
                    ->all()
                : [];

            $calculo = $this->calculador->calcular(
                tipoHonorario: $row->tipo_honorario,
                montoFijo:     $row->monto_fijo !== null ? (float) $row->monto_fijo : null,
                montoPorDia:   $row->monto_por_dia !== null ? (float) $row->monto_por_dia : null,
                diasSemana:    $diasSemana,
                fechaInicio:   $inicioMes,
                fechaFin:      $finMes,
            );

            $yaRegistrado = DB::table('web_sueldo_docente')
                ->where('id_us', $row->id_us)
                ->where('id_programa', $row->id_programa)
                ->where('periodo', sprintf('%04d-%02d', $anio, $mes))
                ->exists();

            return new DocenteHonorarioSugeridoDTO(
                id_us:           (int) $row->id_us,
                docente_nombre:  $row->docente_nombre,
                id_imp:          $row->id_imp !== null ? (int) $row->id_imp : 0,
                nombre_curso:    $row->nombre_curso,
                id_programa:     (int) $row->id_programa,
                nombre_programa: $row->nombre_programa,
                tipo_honorario:  $row->tipo_honorario,
                dias_dictados:   $calculo['dias_dictados'],
                monto_sugerido:  $calculo['monto_sugerido'],
                ya_registrado:   $yaRegistrado,
            );
        })
        ->values()
        ->all();
    }
}
