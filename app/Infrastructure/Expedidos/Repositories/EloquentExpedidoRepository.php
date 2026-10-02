<?php

namespace App\Infrastructure\Expedidos\Repositories;

use App\Application\Expedidos\DTOs\ExpedidoDTO;
use App\Domain\Expedidos\Contracts\ExpedidoRepositoryInterface;
use App\Domain\Expedidos\Exceptions\ExpedidoNotFoundException;
use App\Infrastructure\Expedidos\Models\Expedido;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentExpedidoRepository implements ExpedidoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $query): array
    {
        $q = Expedido::query();

        if ($query) {
            $q->where('nombre', 'like', "%{$query}%");
        }

        $total = $q->count();
        $data  = (clone $q)->orderBy('orden')->orderBy('nombre')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($row) => ExpedidoDTO::fromRow($row))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): mixed
    {
        $row = Expedido::find($id);
        if (! $row) {
            throw new ExpedidoNotFoundException($id);
        }

        return $row;
    }

    public function create(array $data): mixed
    {
        return Expedido::create($data);
    }

    public function update(int $id, array $data): void
    {
        Expedido::where('id', $id)->update($data);
    }

    public function delete(int $id): void
    {
        Expedido::where('id', $id)->delete();
    }
}
