<?php

namespace App\Infrastructure\Acreditaciones\Repositories;

use App\Application\Acreditaciones\DTOs\AcreditacionDTO;
use App\Domain\Acreditaciones\Contracts\AcreditacionRepositoryInterface;
use App\Domain\Acreditaciones\Exceptions\AcreditacionNotFoundException;
use App\Infrastructure\Acreditaciones\Models\Acreditacion;

class EloquentAcreditacionRepository implements AcreditacionRepositoryInterface
{
    public function paginate(int $page, int $pageSize, ?string $query, ?bool $activo): array
    {
        $q = Acreditacion::query();

        if ($query) {
            $q->where('nombre', 'like', "%{$query}%");
        }

        if ($activo !== null) {
            $q->where('activo', $activo);
        }

        $paginated = $q->orderBy('orden')
            ->paginate($pageSize, ['*'], 'page', $page);

        return [
            'data'  => collect($paginated->items())->map(fn ($m) => AcreditacionDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): AcreditacionDTO
    {
        $model = Acreditacion::find($id);

        if (! $model) {
            throw new AcreditacionNotFoundException($id);
        }

        return AcreditacionDTO::fromModel($model);
    }

    public function create(array $data): AcreditacionDTO
    {
        return AcreditacionDTO::fromModel(Acreditacion::create($data));
    }

    public function update(int $id, array $data): AcreditacionDTO
    {
        $model = Acreditacion::find($id);

        if (! $model) {
            throw new AcreditacionNotFoundException($id);
        }

        $model->update($data);

        return AcreditacionDTO::fromModel($model);
    }

    public function delete(int $id): void
    {
        $model = Acreditacion::find($id);

        if (! $model) {
            throw new AcreditacionNotFoundException($id);
        }

        $model->delete();
    }
}
