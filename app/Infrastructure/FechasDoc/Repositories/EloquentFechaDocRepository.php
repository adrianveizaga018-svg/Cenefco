<?php

namespace App\Infrastructure\FechasDoc\Repositories;

use App\Application\FechasDoc\DTOs\FechaDocDTO;
use App\Domain\FechasDoc\Contracts\FechaDocRepositoryInterface;
use App\Domain\FechasDoc\Exceptions\FechaDocNotFoundException;
use App\Infrastructure\FechasDoc\Models\FechaDoc;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentFechaDocRepository implements FechaDocRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $conInactivos, ?int $idPlandoc): array
    {
        $q = FechaDoc::query();

        if (! $conInactivos) {
            $q->where('estado', 1);
        }
        if ($idPlandoc !== null) {
            $q->where('id_plandoc', $idPlandoc);
        }

        $total = $q->count();
        $data  = $q->orderBy('fecha_inicio')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($f) => FechaDocDTO::fromModel($f))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): FechaDocDTO
    {
        $row = FechaDoc::where('id_fechadoc', $id)->first();
        if (! $row) {
            throw new FechaDocNotFoundException($id);
        }

        return FechaDocDTO::fromModel($row);
    }

    public function create(array $data): FechaDocDTO
    {
        return FechaDocDTO::fromModel(FechaDoc::create($data));
    }

    public function update(int $id, array $data): FechaDocDTO
    {
        $row = FechaDoc::where('id_fechadoc', $id)->first();
        if (! $row) {
            throw new FechaDocNotFoundException($id);
        }

        $row->update($data);

        return FechaDocDTO::fromModel($row);
    }

    public function delete(int $id): void
    {
        $row = FechaDoc::where('id_fechadoc', $id)->first();
        if (! $row) {
            throw new FechaDocNotFoundException($id);
        }

        $row->update(['estado' => 0]);
    }
}
