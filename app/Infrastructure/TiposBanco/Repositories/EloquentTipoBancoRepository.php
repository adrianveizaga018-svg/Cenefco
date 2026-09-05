<?php

namespace App\Infrastructure\TiposBanco\Repositories;

use App\Application\TiposBanco\DTOs\TipoBancoDTO;
use App\Domain\TiposBanco\Contracts\TipoBancoRepositoryInterface;
use App\Domain\TiposBanco\Exceptions\TipoBancoNotFoundException;
use App\Infrastructure\TiposBanco\Models\TipoBanco;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentTipoBancoRepository implements TipoBancoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array
    {
        $q = TipoBanco::query();

        if ($pagination->query) {
            $q->where('nombre', 'like', "%{$pagination->query}%");
        }

        $paginated = $q->orderBy('orden')
            ->orderBy('nombre')
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($m) => TipoBancoDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findAllActivos(): array
    {
        return TipoBanco::where('activo', true)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get()
            ->map(fn ($m) => TipoBancoDTO::fromModel($m))
            ->all();
    }

    public function findById(int $id): mixed
    {
        $model = TipoBanco::find($id);
        if (! $model) {
            throw new TipoBancoNotFoundException($id);
        }

        return $model;
    }

    public function create(array $data): mixed
    {
        return TipoBanco::create($data);
    }

    public function update(int $id, array $data): mixed
    {
        $model = TipoBanco::find($id);
        if (! $model) {
            throw new TipoBancoNotFoundException($id);
        }
        $model->update($data);

        return $model;
    }

    public function delete(int $id): bool
    {
        $model = TipoBanco::find($id);
        if (! $model) {
            throw new TipoBancoNotFoundException($id);
        }

        return (bool) $model->delete();
    }
}
