<?php

namespace App\Infrastructure\Trivia\Repositories;

use App\Application\Trivia\DTOs\TriviaPremioDTO;
use App\Domain\Trivia\Contracts\TriviaPremioRepositoryInterface;
use App\Infrastructure\Trivia\Models\TriviaPremio;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentTriviaPremioRepository implements TriviaPremioRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array
    {
        $q = TriviaPremio::query();

        if ($pagination->query) {
            $q->where('nombre', 'like', "%{$pagination->query}%");
        }

        $paginated = $q->orderBy($pagination->sortKey === 'created_at' ? 'orden' : $pagination->sortKey, $pagination->sortOrder)
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data' => collect($paginated->items())->map(fn ($m) => TriviaPremioDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): mixed
    {
        return TriviaPremio::find($id);
    }

    public function activos(): array
    {
        return TriviaPremio::query()
            ->where('activo', true)
            ->where(function ($q) {
                $q->whereNull('stock')->orWhere('stock', '>', 0);
            })
            ->orderBy('orden')
            ->get()
            ->all();
    }

    public function findByIdConLock(int $id): mixed
    {
        return TriviaPremio::query()->where('id', $id)->lockForUpdate()->first();
    }

    public function create(array $data): mixed
    {
        return TriviaPremio::create($data);
    }

    public function update(int $id, array $data): mixed
    {
        $model = TriviaPremio::find($id);
        $model->update($data);

        return $model;
    }

    public function delete(int $id): bool
    {
        return TriviaPremio::destroy($id) > 0;
    }

    public function decrementarStock(int $id): void
    {
        TriviaPremio::where('id', $id)->where('stock', '>', 0)->decrement('stock');
    }

    public function incrementarStock(int $id): void
    {
        TriviaPremio::where('id', $id)->increment('stock');
    }
}
