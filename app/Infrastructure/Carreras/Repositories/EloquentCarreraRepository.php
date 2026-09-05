<?php

namespace App\Infrastructure\Carreras\Repositories;

use App\Application\Carreras\DTOs\CarreraDTO;
use App\Domain\Carreras\Contracts\CarreraRepositoryInterface;
use App\Domain\Carreras\Exceptions\CarreraNotFoundException;
use App\Infrastructure\Carreras\Models\Carrera;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentCarreraRepository implements CarreraRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $conInactivos): array
    {
        $q = Carrera::query();

        if (! $conInactivos) {
            $q->where('estado', 1);
        }

        if ($pagination->query) {
            $q->where('nombre_carrera', 'like', "%{$pagination->query}%");
        }

        $total = $q->count();
        $data  = $q->orderBy('nombre_carrera')
            ->offset(($pagination->pageIndex - 1) * $pagination->pageSize)
            ->limit($pagination->pageSize)
            ->get()
            ->map(fn ($c) => CarreraDTO::fromModel($c))
            ->all();

        return ['data' => $data, 'total' => $total];
    }

    public function findById(int $id): CarreraDTO
    {
        $carrera = Carrera::where('id_carrera', $id)->first();
        if (! $carrera) {
            throw new CarreraNotFoundException($id);
        }

        return CarreraDTO::fromModel($carrera);
    }

    public function create(array $data): CarreraDTO
    {
        $carrera = Carrera::create($data);

        return CarreraDTO::fromModel($carrera);
    }

    public function update(int $id, array $data): CarreraDTO
    {
        $carrera = Carrera::where('id_carrera', $id)->first();
        if (! $carrera) {
            throw new CarreraNotFoundException($id);
        }

        $carrera->update($data);

        return CarreraDTO::fromModel($carrera);
    }

    public function delete(int $id): void
    {
        $carrera = Carrera::where('id_carrera', $id)->first();
        if (! $carrera) {
            throw new CarreraNotFoundException($id);
        }

        $carrera->update(['estado' => 0]);
    }
}
