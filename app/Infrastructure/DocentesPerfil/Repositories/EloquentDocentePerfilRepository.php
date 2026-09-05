<?php

namespace App\Infrastructure\DocentesPerfil\Repositories;

use App\Application\DocentesPerfil\DTOs\DocentePerfilDTO;
use App\Domain\DocentesPerfil\Contracts\DocentePerfilRepositoryInterface;
use App\Domain\DocentesPerfil\Exceptions\DocentePerfilNotFoundException;
use App\Infrastructure\DocentesPerfil\Models\DocentePerfil;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Support\Facades\DB;

class EloquentDocentePerfilRepository implements DocentePerfilRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloVisibles = false): array
    {
        $q = DocentePerfil::query();

        if ($soloVisibles) {
            $q->where('mostrar_en_web', true);
        }

        if ($pagination->query) {
            $q->where(function ($sq) use ($pagination) {
                $sq->where('nombre_completo', 'like', "%{$pagination->query}%")
                   ->orWhere('especialidad', 'like', "%{$pagination->query}%");
            });
        }

        $paginated = $q->orderBy('orden')
            ->orderBy('nombre_completo')
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($m) => DocentePerfilDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): DocentePerfilDTO
    {
        $model = DocentePerfil::find($id);
        if (! $model) {
            throw new DocentePerfilNotFoundException($id);
        }

        return DocentePerfilDTO::fromModel($model);
    }

    public function findBySlug(string $slug): DocentePerfilDTO
    {
        $model = DocentePerfil::where('slug', $slug)->first();
        if (! $model) {
            throw new DocentePerfilNotFoundException($slug);
        }

        return DocentePerfilDTO::fromModel($model);
    }

    public function create(array $data): DocentePerfilDTO
    {
        return DocentePerfilDTO::fromModel(DocentePerfil::create($data));
    }

    public function update(int $id, array $data): DocentePerfilDTO
    {
        $model = DocentePerfil::find($id);
        if (! $model) {
            throw new DocentePerfilNotFoundException($id);
        }
        $model->update($data);

        return DocentePerfilDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return DocentePerfil::destroy($ids) > 0;
    }

    public function reporteMaterias(?string $gestion, ?string $periodo): array
    {
        $imparteQuery = DB::table('t_imparte as i')
            ->select('i.id_imp', 'i.id_us')
            ->whereRaw('i.id_us_reg = (SELECT MIN(i2.id_us_reg) FROM t_imparte i2 WHERE i2.id_imp = i.id_imp)')
            ->where('i.estado', 1);

        if ($gestion) {
            $imparteQuery->where('i.gestion', $gestion);
        }
        if ($periodo) {
            $imparteQuery->where('i.periodo', $periodo);
        }

        return DB::table('web_docente_perfil as d')
            ->leftJoinSub($imparteQuery, 'imp', fn ($j) => $j->on('d.usuario_id', '=', 'imp.id_us'))
            ->whereNull('d.deleted_at')
            ->select([
                'd.id',
                'd.nombre_completo',
                'd.titulo_academico',
                'd.especialidad',
                DB::raw("COALESCE(d.telefono, '') as telefono"),
                DB::raw("COALESCE(d.email_publico, '') as email_publico"),
                'd.tipo',
                'd.estado',
                DB::raw('COUNT(imp.id_imp) as total_materias'),
            ])
            ->groupBy('d.id', 'd.nombre_completo', 'd.titulo_academico', 'd.especialidad', 'd.telefono', 'd.email_publico', 'd.tipo', 'd.estado')
            ->orderByDesc(DB::raw('COUNT(imp.id_imp)'))
            ->orderBy('d.nombre_completo')
            ->get()
            ->all();
    }
}
