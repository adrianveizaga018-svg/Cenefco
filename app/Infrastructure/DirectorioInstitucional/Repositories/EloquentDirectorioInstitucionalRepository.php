<?php

namespace App\Infrastructure\DirectorioInstitucional\Repositories;

use App\Application\DirectorioInstitucional\DTOs\DirectorioInstitucionalDTO;
use App\Domain\DirectorioInstitucional\Contracts\DirectorioInstitucionalRepositoryInterface;
use App\Domain\DirectorioInstitucional\Exceptions\DirectorioInstitucionalNotFoundException;
use App\Infrastructure\DirectorioInstitucional\Models\DirectorioInstitucional;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentDirectorioInstitucionalRepository implements DirectorioInstitucionalRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array
    {
        $q = DirectorioInstitucional::with('secretaria');

        if ($pagination->query) {
            $q->where(function ($sub) use ($pagination) {
                $sub->where('nombre_unidad', 'like', "%{$pagination->query}%")
                    ->orWhere('titular', 'like', "%{$pagination->query}%")
                    ->orWhere('email_institucional', 'like', "%{$pagination->query}%");
            });
        }

        if ($soloActivos) {
            $q->where('activo', true);
        }

        $paginated = $q->orderBy('orden')
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data' => collect($paginated->items())->map(fn ($m) => DirectorioInstitucionalDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): DirectorioInstitucionalDTO
    {
        $model = DirectorioInstitucional::with('secretaria')->find($id);
        if (! $model) {
            throw new DirectorioInstitucionalNotFoundException($id);
        }

        return DirectorioInstitucionalDTO::fromModel($model);
    }

    public function create(array $data): DirectorioInstitucionalDTO
    {
        $model = DirectorioInstitucional::create($data);
        $model->load('secretaria');

        return DirectorioInstitucionalDTO::fromModel($model);
    }

    public function update(int $id, array $data): DirectorioInstitucionalDTO
    {
        $model = DirectorioInstitucional::find($id);
        if (! $model) {
            throw new DirectorioInstitucionalNotFoundException($id);
        }
        $model->update($data);
        $model->load('secretaria');

        return DirectorioInstitucionalDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return DirectorioInstitucional::destroy($ids) > 0;
    }
}
