<?php

namespace App\Infrastructure\Boletines\Repositories;

use App\Application\Boletines\DTOs\BoletinDTO;
use App\Domain\Boletines\Contracts\BoletinRepositoryInterface;
use App\Domain\Boletines\Exceptions\BoletinNotFoundException;
use App\Infrastructure\Boletines\Models\Boletin;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentBoletinRepository implements BoletinRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $query, bool $conInactivos): array
    {
        $q = Boletin::query();

        if ($query) {
            $q->where('titulo_boletin', 'like', "%{$query}%");
        }
        if (! $conInactivos) {
            $q->where('estado', 1);
        }

        $total = $q->count();
        $data  = $q->orderByDesc('id_boletin')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($row) => BoletinDTO::fromRow($row))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): mixed
    {
        $row = Boletin::find($id);
        if (! $row) {
            throw new BoletinNotFoundException($id);
        }

        return $row;
    }

    public function findBySlug(string $slug): mixed
    {
        $row = Boletin::where('slug', $slug)->first();
        if (! $row) {
            throw new BoletinNotFoundException($slug);
        }

        return $row;
    }

    public function create(array $data): mixed
    {
        return Boletin::create($data);
    }

    public function update(int $id, array $data): void
    {
        Boletin::where('id_boletin', $id)->update($data);
    }

    public function softDelete(int $id): void
    {
        Boletin::where('id_boletin', $id)->update(['estado' => 0]);
    }
}
