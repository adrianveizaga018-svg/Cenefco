<?php

namespace App\Infrastructure\Ayudas\Repositories;

use App\Application\Ayudas\DTOs\AyudaDTO;
use App\Domain\Ayudas\Contracts\AyudaRepositoryInterface;
use App\Domain\Ayudas\Exceptions\AyudaNotFoundException;
use App\Infrastructure\Ayudas\Models\Ayuda;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentAyudaRepository implements AyudaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $idUs, ?string $gestion, bool $conInactivos): array
    {
        $q = Ayuda::query();

        if ($idUs !== null) {
            $q->where('id_us', $idUs);
        }
        if ($gestion !== null) {
            $q->where('gestion', $gestion);
        }
        if (! $conInactivos) {
            $q->where('estado', 1);
        }

        $total = $q->count();
        $data  = $q->orderByDesc('fecha_recibo')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($row) => AyudaDTO::fromRow($row))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): mixed
    {
        $row = Ayuda::find($id);
        if (! $row) {
            throw new AyudaNotFoundException($id);
        }

        return $row;
    }

    public function create(array $data): mixed
    {
        return Ayuda::create($data);
    }

    public function update(int $id, array $data): void
    {
        Ayuda::where('id_ayuda', $id)->update($data);
    }

    public function softDelete(int $id): void
    {
        Ayuda::where('id_ayuda', $id)->update(['estado' => 0]);
    }
}
