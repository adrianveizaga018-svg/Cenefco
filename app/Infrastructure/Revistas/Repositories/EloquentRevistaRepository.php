<?php

namespace App\Infrastructure\Revistas\Repositories;

use App\Application\Revistas\DTOs\RevistaDTO;
use App\Domain\Revistas\Contracts\RevistaRepositoryInterface;
use App\Domain\Revistas\Exceptions\RevistaNotFoundException;
use App\Infrastructure\Revistas\Models\Revista;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentRevistaRepository implements RevistaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $query, bool $conInactivos): array
    {
        $q = Revista::query();

        if ($query) {
            $q->where('titulo_revista', 'like', "%{$query}%");
        }
        if (! $conInactivos) {
            $q->where('estado', 1);
        }

        $total = $q->count();
        $data  = $q->orderByDesc('fecha_publicacion')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($row) => RevistaDTO::fromRow($row))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): mixed
    {
        $row = Revista::find($id);
        if (! $row) {
            throw new RevistaNotFoundException($id);
        }

        return $row;
    }

    public function create(array $data): mixed
    {
        return Revista::create($data);
    }

    public function update(int $id, array $data): void
    {
        Revista::where('id_revista', $id)->update($data);
    }

    public function softDelete(int $id): void
    {
        Revista::where('id_revista', $id)->update(['estado' => 0]);
    }
}
