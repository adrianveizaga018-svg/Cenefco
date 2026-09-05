<?php

namespace App\Infrastructure\MediosPago\Repositories;

use App\Application\MediosPago\DTOs\MedioPagoDTO;
use App\Domain\MediosPago\Contracts\MedioPagoRepositoryInterface;
use App\Domain\MediosPago\Exceptions\MedioPagoNotFoundException;
use App\Infrastructure\MediosPago\Models\MedioPago;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentMedioPagoRepository implements MedioPagoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $query, bool $soloActivos = false): array
    {
        $q = MedioPago::query();

        if ($query) {
            $q->where('nombre', 'like', "%{$query}%");
        }

        if ($soloActivos) {
            $q->where('activo', true);
        }

        $total = $q->count();
        $data  = (clone $q)->orderBy('orden')->orderBy('nombre')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($row) => MedioPagoDTO::fromRow($row))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): mixed
    {
        $row = MedioPago::find($id);
        if (! $row) {
            throw new MedioPagoNotFoundException($id);
        }

        return $row;
    }

    public function create(array $data): mixed
    {
        return MedioPago::create($data);
    }

    public function update(int $id, array $data): void
    {
        MedioPago::where('id', $id)->update($data);
    }

    public function delete(int $id): void
    {
        MedioPago::where('id', $id)->delete();
    }
}
