<?php

namespace App\Infrastructure\Monografias\Repositories;

use App\Application\Monografias\DTOs\MonografiaDTO;
use App\Domain\Monografias\Contracts\MonografiaRepositoryInterface;
use App\Domain\Monografias\Exceptions\MonografiaNotFoundException;
use App\Infrastructure\Monografias\Models\Monografia;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentMonografiaRepository implements MonografiaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $query, bool $conInactivos): array
    {
        $q = Monografia::query();

        if ($query) {
            $q->where(function ($sq) use ($query) {
                $sq->where('titulo_monografia', 'like', "%{$query}%")
                   ->orWhere('autor', 'like', "%{$query}%");
            });
        }
        if (! $conInactivos) {
            $q->where('estado', 1);
        }

        $total = $q->count();
        $data  = $q->orderByDesc('fecha_publicacion')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($row) => MonografiaDTO::fromRow($row))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): mixed
    {
        $row = Monografia::find($id);
        if (! $row) {
            throw new MonografiaNotFoundException($id);
        }

        return $row;
    }

    public function create(array $data): mixed
    {
        return Monografia::create($data);
    }

    public function update(int $id, array $data): void
    {
        Monografia::where('id_monografia', $id)->update($data);
    }

    public function softDelete(int $id): void
    {
        Monografia::where('id_monografia', $id)->update(['estado' => 0]);
    }
}
