<?php

namespace App\Infrastructure\CartaGens\Repositories;

use App\Application\CartaGens\DTOs\CartaGenDTO;
use App\Domain\CartaGens\Contracts\CartaGenRepositoryInterface;
use App\Domain\CartaGens\Exceptions\CartaGenNotFoundException;
use App\Infrastructure\CartaGens\Models\CartaGen;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentCartaGenRepository implements CartaGenRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $idUs, ?int $idCartamod, bool $conInactivos): array
    {
        $q = CartaGen::query();

        if ($idUs !== null) {
            $q->where('id_us', $idUs);
        }
        if ($idCartamod !== null) {
            $q->where('id_cartamod', $idCartamod);
        }
        if (! $conInactivos) {
            $q->where('estado', 1);
        }

        $total = $q->count();
        $data  = $q->orderByDesc('id_cartagen')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($row) => CartaGenDTO::fromRow($row))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): mixed
    {
        $row = CartaGen::find($id);
        if (! $row) {
            throw new CartaGenNotFoundException($id);
        }

        return $row;
    }

    public function create(array $data): mixed
    {
        return CartaGen::create($data);
    }

    public function update(int $id, array $data): void
    {
        CartaGen::where('id_cartagen', $id)->update($data);
    }

    public function softDelete(int $id): void
    {
        CartaGen::where('id_cartagen', $id)->update(['estado' => 0]);
    }
}
