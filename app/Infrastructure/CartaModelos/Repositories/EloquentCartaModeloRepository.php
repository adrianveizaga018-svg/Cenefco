<?php

namespace App\Infrastructure\CartaModelos\Repositories;

use App\Application\CartaModelos\DTOs\CartaModeloDTO;
use App\Domain\CartaModelos\Contracts\CartaModeloRepositoryInterface;
use App\Domain\CartaModelos\Exceptions\CartaModeloNotFoundException;
use App\Infrastructure\CartaModelos\Models\CartaModelo;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentCartaModeloRepository implements CartaModeloRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $query, bool $conInactivos): array
    {
        $q = CartaModelo::query();

        if ($query) {
            $q->where('nombremodelo', 'like', "%{$query}%");
        }
        if (! $conInactivos) {
            $q->where('estado', 1);
        }

        $total = $q->count();
        $data  = $q->orderBy('nombremodelo')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($row) => CartaModeloDTO::fromRow($row))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): mixed
    {
        $row = CartaModelo::find($id);
        if (! $row) {
            throw new CartaModeloNotFoundException($id);
        }

        return $row;
    }

    public function create(array $data): mixed
    {
        return CartaModelo::create($data);
    }

    public function update(int $id, array $data): void
    {
        CartaModelo::where('id_cartamod', $id)->update($data);
    }

    public function softDelete(int $id): void
    {
        CartaModelo::where('id_cartamod', $id)->update(['estado' => 0]);
    }
}
