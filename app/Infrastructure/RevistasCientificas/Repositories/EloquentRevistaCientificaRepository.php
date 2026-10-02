<?php

namespace App\Infrastructure\RevistasCientificas\Repositories;

use App\Application\RevistasCientificas\DTOs\RevistaCientificaDTO;
use App\Domain\RevistasCientificas\Contracts\RevistaCientificaRepositoryInterface;
use App\Domain\RevistasCientificas\Exceptions\RevistaCientificaNotFoundException;
use App\Infrastructure\RevistasCientificas\Models\RevistaCientifica;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentRevistaCientificaRepository implements RevistaCientificaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $query, bool $conInactivos): array
    {
        $q = RevistaCientifica::query();

        if ($query) {
            $q->where('titulo_revistacientifica', 'like', "%{$query}%");
        }
        if (! $conInactivos) {
            $q->where('estado', 1);
        }

        $total = $q->count();
        $data  = $q->orderByDesc('fecha_publicacion')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($row) => RevistaCientificaDTO::fromRow($row))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): mixed
    {
        $row = RevistaCientifica::find($id);
        if (! $row) {
            throw new RevistaCientificaNotFoundException($id);
        }

        return $row;
    }

    public function create(array $data): mixed
    {
        return RevistaCientifica::create($data);
    }

    public function update(int $id, array $data): void
    {
        RevistaCientifica::where('id_revistacientifica', $id)->update($data);
    }

    public function softDelete(int $id): void
    {
        RevistaCientifica::where('id_revistacientifica', $id)->update(['estado' => 0]);
    }
}
