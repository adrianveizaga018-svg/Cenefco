<?php

namespace App\Infrastructure\DirectorioArchivos\Repositories;

use App\Application\DirectorioArchivos\DTOs\ArchivoParticipanteDTO;
use App\Application\DirectorioArchivos\DTOs\CursoDirectorioDTO;
use App\Application\DirectorioArchivos\DTOs\ParticipanteDirectorioDTO;
use App\Domain\DirectorioArchivos\Contracts\DirectorioArchivosRepositoryInterface;
use App\Domain\DirectorioArchivos\Exceptions\ParticipanteArchivosNotFoundException;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Support\Facades\DB;

class EloquentDirectorioArchivosRepository implements DirectorioArchivosRepositoryInterface
{
    public function paginateCursos(PaginationDTO $pagination, ?array $idImpPermitidos): array
    {
        $conDocumentos = DB::table('t_inscripcion')
            ->selectRaw('id_imp, COUNT(*) as total')
            ->where('estado', 1)
            ->whereNotNull('documentos')
            ->whereRaw("jsonb_typeof(documentos) = 'object'")
            ->whereRaw("documentos <> '{}'::jsonb")
            ->groupBy('id_imp');

        $q = DB::table('t_imparte as imp')
            ->join('t_materia as mat', function ($j) {
                $j->on('imp.id_mat', '=', 'mat.id_mat')
                  ->whereRaw('mat.id_us_reg = (SELECT MIN(m2.id_us_reg) FROM t_materia m2 WHERE m2.id_mat = mat.id_mat)');
            })
            ->leftJoin('t_programa as prog', 'prog.id_imp', '=', 'imp.id_imp')
            ->leftJoin('t_usuario as doc', function ($j) {
                $j->on('imp.id_us', '=', 'doc.id_us')
                  ->whereRaw('doc.id_us_reg = (SELECT MIN(d2.id_us_reg) FROM t_usuario d2 WHERE d2.id_us = doc.id_us AND d2.tipoestudiante = \'1\')');
            })
            ->joinSub($conDocumentos, 'cnt', fn ($j) => $j->on('cnt.id_imp', '=', 'imp.id_imp'))
            ->when($idImpPermitidos !== null, fn ($qq) => $qq->whereIn('imp.id_imp', $idImpPermitidos))
            ->groupBy(
                'imp.id_imp', 'imp.periodo', 'imp.gestion',
                'mat.id_mat', 'mat.nombre',
                'prog.id_programa', 'prog.nombre_programa',
                'doc.appaterno', 'doc.apmaterno', 'doc.nombre',
                'cnt.total'
            )
            ->selectRaw("
                imp.id_imp,
                COALESCE(prog.nombre_programa, mat.nombre) as nombre,
                imp.periodo,
                imp.gestion,
                TRIM(CONCAT(COALESCE(doc.appaterno,''), ' ', COALESCE(doc.nombre,''))) as docente,
                cnt.total as participantes_con_archivos
            ");

        if ($pagination->query) {
            $search = $pagination->query;
            $q->havingRaw(
                "COALESCE(prog.nombre_programa, mat.nombre) LIKE ? OR TRIM(CONCAT(COALESCE(doc.appaterno,''), ' ', COALESCE(doc.nombre,''))) LIKE ?",
                ["%{$search}%", "%{$search}%"]
            );
        }

        $total = DB::query()->fromSub($q, 'sub')->count();

        $items = $q->orderByDesc('imp.gestion')
            ->orderBy('nombre')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get();

        return [
            'data'  => $items->map(fn ($row) => CursoDirectorioDTO::fromRow($row))->all(),
            'total' => $total,
        ];
    }

    public function paginateParticipantes(int $idImp, PaginationDTO $pagination): array
    {
        $q = DB::table('t_inscripcion as ins')
            ->leftJoin('t_usuario as u', function ($j) {
                $j->on('ins.id_us', '=', 'u.id_us')
                  ->whereRaw('u.id_us_reg = (SELECT MIN(u2.id_us_reg) FROM t_usuario u2 WHERE u2.id_us = u.id_us)');
            })
            ->where('ins.id_imp', $idImp)
            ->where('ins.estado', 1)
            ->whereNotNull('ins.documentos')
            ->whereRaw("jsonb_typeof(ins.documentos) = 'object'")
            ->whereRaw("ins.documentos <> '{}'::jsonb")
            ->select([
                'ins.id_ins', 'ins.id_us', 'ins.fecha_ins',
                'u.ci',
                DB::raw("COALESCE(ins.email, u.email) as email"),
                DB::raw("TRIM(CONCAT(COALESCE(u.nombre,''), ' ', COALESCE(u.appaterno,''))) as nombre_completo"),
                DB::raw("(SELECT COUNT(*) FROM jsonb_each_text(ins.documentos) AS d(k, v) WHERE v IS NOT NULL AND v <> '') as total_archivos"),
            ]);

        if ($pagination->query) {
            $search = $pagination->query;
            $q->where(function ($sq) use ($search) {
                $sq->where('u.nombre', 'like', "%{$search}%")
                   ->orWhere('u.appaterno', 'like', "%{$search}%")
                   ->orWhere('u.ci', 'like', "%{$search}%");
            });
        }

        $total = (clone $q)->count();

        $items = $q->orderByDesc('ins.fecha_ins')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get();

        return [
            'data'  => $items->map(fn ($row) => ParticipanteDirectorioDTO::fromRow($row))->all(),
            'total' => $total,
        ];
    }

    public function archivosDeParticipante(int $idIns): ArchivoParticipanteDTO
    {
        $row = DB::table('t_inscripcion as ins')
            ->leftJoin('t_usuario as u', function ($j) {
                $j->on('ins.id_us', '=', 'u.id_us')
                  ->whereRaw('u.id_us_reg = (SELECT MIN(u2.id_us_reg) FROM t_usuario u2 WHERE u2.id_us = u.id_us)');
            })
            ->leftJoin('t_imparte as imp', 'imp.id_imp', '=', 'ins.id_imp')
            ->leftJoin('t_materia as mat', function ($j) {
                $j->on('imp.id_mat', '=', 'mat.id_mat')
                  ->whereRaw('mat.id_us_reg = (SELECT MIN(m2.id_us_reg) FROM t_materia m2 WHERE m2.id_mat = mat.id_mat)');
            })
            ->leftJoin('t_programa as prog', 'prog.id_imp', '=', 'imp.id_imp')
            ->where('ins.id_ins', $idIns)
            ->select([
                'ins.id_ins', 'ins.id_us', 'ins.documentos', 'u.ci',
                DB::raw("TRIM(CONCAT(COALESCE(u.nombre,''), ' ', COALESCE(u.appaterno,''))) as nombre_completo"),
                DB::raw("COALESCE(prog.nombre_programa, mat.nombre) as curso_nombre"),
            ])
            ->first();

        if (! $row) {
            throw new ParticipanteArchivosNotFoundException($idIns);
        }

        $docs = is_string($row->documentos)
            ? (json_decode($row->documentos, true) ?? [])
            : (array) ($row->documentos ?? []);

        return new ArchivoParticipanteDTO(
            id_ins: (int) $row->id_ins,
            id_us: (int) $row->id_us,
            nombre_completo: $row->nombre_completo,
            ci: $row->ci,
            curso_nombre: $row->curso_nombre,
            archivos: array_filter($docs),
        );
    }
}
