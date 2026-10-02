<?php

namespace App\Infrastructure\Gastos\Repositories;

use App\Application\Gastos\DTOs\CategoriaGastoDTO;
use App\Domain\Gastos\Contracts\CategoriaGastoRepositoryInterface;
use App\Domain\Gastos\Exceptions\CategoriaGastoNotFoundException;
use App\Infrastructure\Gastos\Models\CategoriaGasto;

class EloquentCategoriaGastoRepository implements CategoriaGastoRepositoryInterface
{
    public function findAllActivas(): array
    {
        return CategoriaGasto::where('activo', true)
            ->whereNull('deleted_at')
            ->orderBy('nombre')
            ->get()
            ->map(fn ($c) => CategoriaGastoDTO::fromModel($c))
            ->all();
    }

    public function findById(int $id): CategoriaGastoDTO
    {
        $m = CategoriaGasto::whereNull('deleted_at')->find($id);
        if (! $m) {
            throw new CategoriaGastoNotFoundException($id);
        }

        return CategoriaGastoDTO::fromModel($m);
    }

    public function findIdByNombre(string $nombre): ?int
    {
        return CategoriaGasto::whereNull('deleted_at')
            ->where('nombre', $nombre)
            ->value('id');
    }

    public function create(array $data): CategoriaGastoDTO
    {
        $m = CategoriaGasto::create($data);

        return CategoriaGastoDTO::fromModel($m);
    }

    public function update(int $id, array $data): CategoriaGastoDTO
    {
        $m = CategoriaGasto::whereNull('deleted_at')->find($id);
        if (! $m) {
            throw new CategoriaGastoNotFoundException($id);
        }

        $m->update($data);

        return CategoriaGastoDTO::fromModel($m->fresh());
    }

    public function delete(int $id): bool
    {
        $m = CategoriaGasto::whereNull('deleted_at')->find($id);
        if (! $m) {
            throw new CategoriaGastoNotFoundException($id);
        }

        return (bool) $m->delete();
    }
}
