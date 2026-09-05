<?php

namespace App\Infrastructure\Areas\Repositories;

use App\Application\Areas\DTOs\AreaDTO;
use App\Domain\Areas\Contracts\AreaRepositoryInterface;
use App\Domain\Areas\Exceptions\AreaNotFoundException;
use App\Infrastructure\Areas\Models\Area;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Support\Facades\DB;

class EloquentAreaRepository implements AreaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array
    {
        $q = Area::query();

        if ($pagination->query) {
            $q->where('titulo', 'like', "%{$pagination->query}%");
        }

        $paginated = $q->orderBy('orden')->orderBy('titulo')
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($m) => AreaDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): AreaDTO
    {
        $model = Area::find($id);

        if (! $model) {
            throw new AreaNotFoundException($id);
        }

        return AreaDTO::fromModel($model);
    }

    public function findBySlug(string $slug): AreaDTO
    {
        $model = Area::where('slug', $slug)->first();

        if (! $model) {
            throw new AreaNotFoundException($slug);
        }

        return AreaDTO::fromModel($model);
    }

    public function listActivas(): array
    {
        return Area::where('activo', true)
            ->orderBy('orden')
            ->orderBy('titulo')
            ->get()
            ->map(fn ($m) => AreaDTO::fromModel($m))
            ->all();
    }

    public function listActivasConTotalCursos(): array
    {
        $cursosSub = DB::table('t_programa')
            ->selectRaw('area_id, COUNT(*) as total')
            ->where('estado', 1)
            ->where('estado_web', 'publicado')
            ->groupBy('area_id');

        return Area::where('activo', true)
            ->leftJoinSub($cursosSub, 'cursos_agg', 'cursos_agg.area_id', '=', 'web_area.id')
            ->select('web_area.*', DB::raw('COALESCE(cursos_agg.total, 0) as total_cursos'))
            ->orderBy('web_area.orden')
            ->orderBy('web_area.titulo')
            ->get()
            ->map(function ($m) {
                $dto = AreaDTO::fromModel($m);
                return array_merge((array) $dto, ['total_cursos' => (int) $m->total_cursos]);
            })
            ->all();
    }

    public function findBySlugConCursos(string $slug): array
    {
        $model = Area::where('slug', $slug)->where('activo', true)->first();

        if (! $model) {
            throw new AreaNotFoundException($slug);
        }

        $cursos = DB::table('t_programa as p')
            ->leftJoin('web_categoria_programa as cat', 'p.categoria_web_id', '=', 'cat.id')
            ->where('p.area_id', $model->id)
            ->where('p.estado', 1)
            ->where('p.estado_web', 'publicado')
            ->select([
                'p.id_programa', 'p.nombre_programa', 'p.slug',
                'p.descripcion', 'p.foto', 'p.imagen_banner_url',
                'p.inicio_actividades', 'p.creditaje', 'p.inversion',
                'cat.nombre as categoria_nombre',
            ])
            ->orderBy('p.orden')
            ->get();

        return [
            'area'   => AreaDTO::fromModel($model),
            'cursos' => $cursos,
        ];
    }

    public function create(array $data): AreaDTO
    {
        return AreaDTO::fromModel(Area::create($data));
    }

    public function update(int $id, array $data): AreaDTO
    {
        $model = Area::find($id);

        if (! $model) {
            throw new AreaNotFoundException($id);
        }

        $model->update($data);

        return AreaDTO::fromModel($model);
    }

    public function delete(int $id): void
    {
        $model = Area::find($id);

        if (! $model) {
            throw new AreaNotFoundException($id);
        }

        $model->delete();
    }
}
