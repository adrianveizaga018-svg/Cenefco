<?php

namespace App\Infrastructure\Cartas\Repositories;

use App\Application\Cartas\DTOs\CartaDTO;
use App\Domain\Cartas\Contracts\CartaRepositoryInterface;
use App\Domain\Cartas\Exceptions\CartaNotFoundException;
use App\Infrastructure\Cartas\Models\Carta;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentCartaRepository implements CartaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $idUs, ?int $idPlan, bool $conInactivos): array
    {
        $q = Carta::query();

        if ($idUs !== null) {
            $q->where('id_us', $idUs);
        }
        if ($idPlan !== null) {
            $q->where('id_plan', $idPlan);
        }
        if (! $conInactivos) {
            $q->where('estado', 1);
        }

        $total = $q->count();
        $data  = $q->orderByDesc('id_carta')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($row) => CartaDTO::fromRow($row))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): mixed
    {
        $row = Carta::find($id);
        if (! $row) {
            throw new CartaNotFoundException($id);
        }

        return $row;
    }

    public function create(array $data): mixed
    {
        return Carta::create($data);
    }

    public function update(int $id, array $data): void
    {
        Carta::where('id_carta', $id)->update($data);
    }

    public function softDelete(int $id): void
    {
        Carta::where('id_carta', $id)->update(['estado' => 0]);
    }
}
