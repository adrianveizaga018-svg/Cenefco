<?php

namespace App\Infrastructure\DescuentosPromociones\Repositories;

use App\Application\DescuentosPromociones\DTOs\DescuentoPromocionDTO;
use App\Domain\DescuentosPromociones\Contracts\DescuentoPromocionRepositoryInterface;
use App\Domain\DescuentosPromociones\Exceptions\DescuentoPromocionNotFoundException;
use App\Infrastructure\DescuentosPromociones\Models\DescuentoPromocion;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentDescuentoPromocionRepository implements DescuentoPromocionRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array
    {
        $q = DescuentoPromocion::query();

        if ($soloActivos) {
            $q->where('activo', true);
        }

        if ($pagination->query) {
            $term = '%' . $pagination->query . '%';
            $q->where(function ($sq) use ($term) {
                $sq->where('codigo', 'like', $term)
                   ->orWhere('nombre', 'like', $term)
                   ->orWhere('descripcion', 'like', $term);
            });
        }

        $paginated = $q->orderBy('id', 'desc')
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($m) => DescuentoPromocionDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): DescuentoPromocionDTO
    {
        $model = DescuentoPromocion::find($id);
        if (! $model) {
            throw new DescuentoPromocionNotFoundException($id);
        }

        return DescuentoPromocionDTO::fromModel($model);
    }

    public function create(array $data): DescuentoPromocionDTO
    {
        return DescuentoPromocionDTO::fromModel(DescuentoPromocion::create($data));
    }

    public function update(int $id, array $data): DescuentoPromocionDTO
    {
        $model = DescuentoPromocion::find($id);
        if (! $model) {
            throw new DescuentoPromocionNotFoundException($id);
        }
        $model->update($data);

        return DescuentoPromocionDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return DescuentoPromocion::destroy($ids) > 0;
    }
}
