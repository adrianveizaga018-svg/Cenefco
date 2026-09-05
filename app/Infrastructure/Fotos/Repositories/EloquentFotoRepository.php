<?php

namespace App\Infrastructure\Fotos\Repositories;

use App\Application\Fotos\DTOs\FotoDTO;
use App\Domain\Fotos\Contracts\FotoRepositoryInterface;
use App\Domain\Fotos\Exceptions\FotoNotFoundException;
use App\Infrastructure\Fotos\Models\Foto;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentFotoRepository implements FotoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $query, bool $conInactivos): array
    {
        $q = Foto::query();

        if ($query) {
            $q->where('titulo_foto', 'like', "%{$query}%");
        }
        if (! $conInactivos) {
            $q->where('estado', 1);
        }

        $total = $q->count();
        $data  = $q->orderByDesc('fecha_foto')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($row) => FotoDTO::fromRow($row))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): mixed
    {
        $row = Foto::find($id);
        if (! $row) {
            throw new FotoNotFoundException($id);
        }

        return $row;
    }

    public function create(array $data): mixed
    {
        return Foto::create($data);
    }

    public function update(int $id, array $data): void
    {
        Foto::where('id_foto', $id)->update($data);
    }

    public function delete(int $id): void
    {
        Foto::where('id_foto', $id)->delete();
    }
}
