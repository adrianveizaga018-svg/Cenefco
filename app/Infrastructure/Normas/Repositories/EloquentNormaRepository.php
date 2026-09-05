<?php

namespace App\Infrastructure\Normas\Repositories;

use App\Application\Normas\DTOs\NormaDTO;
use App\Domain\Normas\Contracts\NormaRepositoryInterface;
use App\Domain\Normas\Exceptions\NormaNotFoundException;
use App\Infrastructure\Normas\Models\Norma;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentNormaRepository implements NormaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, array $filters = []): array
    {
        $q = Norma::with('tipo');

        if ($pagination->query) {
            $q->where(function ($sub) use ($pagination) {
                $sub->where('titulo', 'like', "%{$pagination->query}%")
                    ->orWhere('numero', 'like', "%{$pagination->query}%")
                    ->orWhere('palabras_clave', 'like', "%{$pagination->query}%");
            });
        }

        if (! empty($filters['tipo_id'])) {
            $q->where('tipo_norma_id', $filters['tipo_id']);
        }

        if (! empty($filters['estado'])) {
            $q->where('estado_vigencia', $filters['estado']);
        }

        if (! empty($filters['soloActivos'])) {
            $q->where('activo', true);
        }

        $paginated = $q->orderBy($pagination->sortKey, $pagination->sortOrder)
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data' => collect($paginated->items())->map(fn ($m) => NormaDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): NormaDTO
    {
        $model = Norma::with('tipo')->find($id);
        if (! $model) {
            throw new NormaNotFoundException($id);
        }

        return NormaDTO::fromModel($model);
    }

    public function findBySlug(string $slug): NormaDTO
    {
        $model = Norma::with('tipo')->where('slug', $slug)->first();
        if (! $model) {
            throw new NormaNotFoundException($slug);
        }

        return NormaDTO::fromModel($model);
    }

    public function create(array $data): NormaDTO
    {
        $model = Norma::create($data);

        return NormaDTO::fromModel($model->load('tipo'));
    }

    public function update(int $id, array $data): NormaDTO
    {
        
        $model = Norma::find($id);
        if (! $model) {
            throw new NormaNotFoundException($id);
        }
        $model->update($data);

        return NormaDTO::fromModel($model->load('tipo'));
    }

    public function delete(int|array $ids): bool
    {
        return Norma::destroy($ids) > 0;
    }
}
