<?php

namespace App\Infrastructure\ListaAprobados\Repositories;

use App\Application\ListaAprobados\DTOs\ListaAprobadosDTO;
use App\Domain\ListaAprobados\Contracts\ListaAprobadosRepositoryInterface;
use App\Domain\ListaAprobados\Exceptions\ListaAprobadosNotFoundException;
use App\Infrastructure\ListaAprobados\Models\ListaAprobados;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Support\Facades\DB;

class EloquentListaAprobadosRepository implements ListaAprobadosRepositoryInterface
{
    private function queryConJoins(): \Illuminate\Database\Query\Builder
    {
        return DB::table('t_lista_aprobados as la')
            ->leftJoin('t_imparte as imp', function ($join) {
                $join->on('imp.id_imp', '=', 'la.imparte_id')
                     ->where('imp.id_us_reg', '=', 0);
            })
            ->leftJoin('t_materia as mat', function ($join) {
                $join->on('mat.id_mat', '=', 'imp.id_mat')
                     ->where('mat.id_us_reg', '=', 0);
            })
            ->leftJoin('t_usuario as us', function ($join) {
                $join->on('us.id_us', '=', 'la.usuario_id')
                     ->where('us.id_us_reg', '=', 0);
            })
            ->leftJoin('t_certificado as cert', function ($join) {
                $join->on('cert.lista_aprobado_id', '=', 'la.id')
                     ->where('cert.estado', '!=', 'anulado');
            })
            ->select([
                'la.*',
                'mat.nombre as curso_nombre',
                'imp.periodo as curso_periodo',
                'imp.gestion as curso_gestion',
                DB::raw("TRIM(CONCAT_WS(' ', NULLIF(us.appaterno,''), NULLIF(us.apmaterno,''), NULLIF(us.nombre,''))) as estudiante_nombre"),
                'us.appaterno as estudiante_appaterno',
                'us.apmaterno as estudiante_apmaterno',
                'us.nombre as estudiante_nombre_pila',
                'us.ci as estudiante_ci',
                'us.email as estudiante_email',
                'cert.archivo_url as cert_archivo_url',
                'cert.codigo_verificacion as cert_codigo',
                'cert.id as cert_id',
            ]);
    }

    public function paginate(
        PaginationDTO $pagination,
        ?int $imparteId,
        ?int $usuarioId,
        ?string $condicion,
        ?string $estadoCertificado,
    ): array {
        $q = $this->queryConJoins();

        if ($imparteId !== null) {
            $q->where('la.imparte_id', $imparteId);
        }
        if ($usuarioId !== null) {
            $q->where('la.usuario_id', $usuarioId);
        }
        if ($condicion !== null) {
            $q->where('la.condicion', $condicion);
        }
        if ($estadoCertificado !== null) {
            $q->where('la.estado_certificado', $estadoCertificado);
        }
        if ($pagination->query) {
            $search = '%' . $pagination->query . '%';
            $q->where(function ($w) use ($search) {
                $w->where('mat.nombre', 'like', $search)
                  ->orWhere('us.appaterno', 'like', $search)
                  ->orWhere('us.nombre', 'like', $search)
                  ->orWhere('us.ci', 'like', $search);
            });
        }

        $total = $q->count();
        $data  = $q->orderByDesc('la.id')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($row) => ListaAprobadosDTO::fromRow($row))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): mixed
    {
        $row = $this->queryConJoins()->where('la.id', $id)->first();
        if (! $row) {
            throw new ListaAprobadosNotFoundException($id);
        }

        return ListaAprobadosDTO::fromRow($row);
    }

    public function existeParaImparteYUsuario(int $imparteId, int $usuarioId): bool
    {
        return ListaAprobados::where('imparte_id', $imparteId)
            ->where('usuario_id', $usuarioId)
            ->exists();
    }

    public function aprobadosPendientesDeCertificado(int $imparteId, ?array $usuarioIds = null): \Illuminate\Support\Collection
    {
        return DB::table('t_lista_aprobados as la')
            ->leftJoin('t_usuario as u', function ($join) {
                $join->on('la.usuario_id', '=', 'u.id_us')
                    ->whereRaw('u.id_us_reg = (SELECT MIN(u2.id_us_reg) FROM t_usuario u2 WHERE u2.id_us = u.id_us)');
            })
            ->select('la.*', 'u.nombre', 'u.appaterno', 'u.apmaterno', 'u.email', 'u.ci')
            ->where('la.imparte_id', $imparteId)
            ->where('la.condicion', 'aprobado')
            ->when($usuarioIds !== null, fn ($q) => $q->whereIn('la.usuario_id', $usuarioIds))
            ->whereNotIn('la.id', function ($sub) {
                $sub->select('lista_aprobado_id')->from('t_certificado')->whereNotNull('lista_aprobado_id');
            })
            ->get();
    }

    public function create(array $data): mixed
    {
        $model = ListaAprobados::create($data);

        return $this->queryConJoins()->where('la.id', $model->id)->first();
    }

    public function update(int $id, array $data): mixed
    {
        $row = ListaAprobados::find($id);
        if (! $row) {
            throw new ListaAprobadosNotFoundException($id);
        }

        $row->fill($data)->save();

        return $this->queryConJoins()->where('la.id', $id)->first();
    }

    public function delete(int $id): bool
    {
        $deleted = ListaAprobados::where('id', $id)->delete();
        if (! $deleted) {
            throw new ListaAprobadosNotFoundException($id);
        }

        return true;
    }

    public function deleteMany(array $ids): int
    {
        if (empty($ids)) {
            return 0;
        }

        return ListaAprobados::whereIn('id', $ids)->delete();
    }
}
