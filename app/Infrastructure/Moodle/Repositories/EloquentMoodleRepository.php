<?php

namespace App\Infrastructure\Moodle\Repositories;

use App\Application\Moodle\DTOs\MoodleDTO;
use App\Domain\Moodle\Contracts\MoodleRepositoryInterface;
use App\Domain\Moodle\Exceptions\MoodleNotFoundException;
use App\Infrastructure\Moodle\Models\MoodleLegado;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentMoodleRepository implements MoodleRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $query, bool $conInactivos): array
    {
        $q = MoodleLegado::query();

        if (! $conInactivos) {
            $q->where('estado', 1);
        }
        if ($query) {
            $q->where('titulo_moodle', 'like', "%{$query}%");
        }

        $paginated = $q->orderBy('titulo_moodle')
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($r) => MoodleDTO::fromRow($r))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): object
    {
        $row = MoodleLegado::find($id);
        if (! $row) {
            throw new MoodleNotFoundException($id);
        }
        return $row;
    }

    public function create(array $data): object
    {
        return MoodleLegado::create($data);
    }

    public function update(int $id, array $data): void
    {
        $this->findById($id)->update($data);
    }

    public function delete(int $id): void
    {
        $this->findById($id)->update(['estado' => 0]);
    }
}
