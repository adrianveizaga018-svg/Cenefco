<?php

namespace App\Infrastructure\Tesis\Repositories;

use App\Application\Tesis\DTOs\TesisDTO;
use App\Domain\Tesis\Contracts\TesisRepositoryInterface;
use App\Domain\Tesis\Exceptions\TesisNotFoundException;
use App\Infrastructure\Tesis\Models\Tesis;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentTesisRepository implements TesisRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $query, ?int $tipoTesis, bool $conInactivos): array
    {
        $q = Tesis::query();

        if ($query) {
            $q->where(function ($sq) use ($query) {
                $sq->where('titulo_tesis', 'like', "%{$query}%")
                   ->orWhere('autor', 'like', "%{$query}%");
            });
        }
        if ($tipoTesis !== null) {
            $q->where('tipo_tesis', $tipoTesis);
        }
        if (! $conInactivos) {
            $q->where('estado', 1);
        }

        $total = $q->count();
        $data  = $q->orderByDesc('fecha_publicacion')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($row) => TesisDTO::fromRow($row))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): mixed
    {
        $row = Tesis::find($id);
        if (! $row) {
            throw new TesisNotFoundException($id);
        }

        return $row;
    }

    public function create(array $data): mixed
    {
        return Tesis::create($data);
    }

    public function update(int $id, array $data): void
    {
        Tesis::where('id_tesis', $id)->update($data);
    }

    public function softDelete(int $id): void
    {
        Tesis::where('id_tesis', $id)->update(['estado' => 0]);
    }
}
