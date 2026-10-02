<?php

namespace App\Infrastructure\HistoriaMunicipio\Repositories;

use App\Application\HistoriaMunicipio\DTOs\HistoriaMunicipioDTO;
use App\Domain\HistoriaMunicipio\Contracts\HistoriaMunicipioRepositoryInterface;
use App\Domain\HistoriaMunicipio\Exceptions\HistoriaMunicipioNotFoundException;
use App\Infrastructure\HistoriaMunicipio\Models\HistoriaMunicipio;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentHistoriaMunicipioRepository implements HistoriaMunicipioRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array
    {
        $q = HistoriaMunicipio::query();

        if ($pagination->query) {
            $q->where(function ($sq) use ($pagination) {
                $sq->where('titulo', 'like', "%{$pagination->query}%")
                    ->orWhere('contenido', 'like', "%{$pagination->query}%");
            });
        }

        $paginated = $q->orderBy('orden')->orderBy('id')
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data' => collect($paginated->items())->map(fn ($m) => HistoriaMunicipioDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): HistoriaMunicipioDTO
    {
        $model = HistoriaMunicipio::find($id);
        if (! $model) {
            throw new HistoriaMunicipioNotFoundException($id);
        }

        return HistoriaMunicipioDTO::fromModel($model);
    }

    public function create(array $data): HistoriaMunicipioDTO
    {
        return HistoriaMunicipioDTO::fromModel(HistoriaMunicipio::create($data));
    }

    public function update(int $id, array $data): HistoriaMunicipioDTO
    {
        $model = HistoriaMunicipio::find($id);
        if (! $model) {
            throw new HistoriaMunicipioNotFoundException($id);
        }
        $model->update($data);

        return HistoriaMunicipioDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return HistoriaMunicipio::destroy($ids) > 0;
    }
}
