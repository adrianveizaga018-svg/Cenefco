<?php

namespace App\Infrastructure\Noticias\Repositories;

use App\Application\Noticias\DTOs\NoticiaDTO;
use App\Domain\Noticias\Contracts\NoticiaRepositoryInterface;
use App\Domain\Noticias\Exceptions\NoticiaNotFoundException;
use App\Infrastructure\Noticias\Models\Noticia;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentNoticiaRepository implements NoticiaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array
    {
        $q = Noticia::query()->with(['categoria', 'etiquetas']);

        if ($soloActivos) {
            $q->where('estado', 'publicado');
        }

        if ($pagination->query) {
            if (\DB::connection()->getDriverName() === 'pgsql') {
                $q->whereRaw(
                    "to_tsvector('spanish', coalesce(titulo,'') || ' ' || coalesce(entradilla,'') || ' ' || coalesce(cuerpo,'')) @@ plainto_tsquery('spanish', ?)",
                    [$pagination->query]
                );
            } else {
                $q->whereFullText(['titulo', 'entradilla', 'cuerpo'], $pagination->query);
            }
        }

        $paginated = $q->orderBy($pagination->sortKey, $pagination->sortOrder)
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data' => collect($paginated->items())->map(fn ($m) => NoticiaDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): NoticiaDTO
    {
        $model = Noticia::with(['categoria', 'etiquetas'])->find($id);
        if (! $model) {
            throw new NoticiaNotFoundException($id);
        }

        return NoticiaDTO::fromModel($model);
    }

    public function findBySlug(string $slug): NoticiaDTO
    {
        $model = Noticia::with(['categoria', 'etiquetas'])->where('slug', $slug)->first();
        if (! $model) {
            throw new NoticiaNotFoundException($slug);
        }

        return NoticiaDTO::fromModel($model);
    }

    public function create(array $data): NoticiaDTO
    {
        $model = Noticia::create($data);

        return NoticiaDTO::fromModel($model);
    }

    public function update(int $id, array $data): NoticiaDTO
    {
        $model = Noticia::find($id);
        if (! $model) {
            throw new NoticiaNotFoundException($id);
        }
        $model->update($data);

        return NoticiaDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return Noticia::destroy($ids) > 0;
    }

    public function syncEtiquetas(int $noticiaId, array $etiquetaIds): void
    {
        $model = Noticia::find($noticiaId);
        if ($model) {
            $model->etiquetas()->sync($etiquetaIds);
        }
    }
}
