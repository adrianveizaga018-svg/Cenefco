<?php

namespace App\Infrastructure\Formularios\Repositories;

use App\Application\Formularios\DTOs\FormularioDTO;
use App\Domain\Formularios\Contracts\FormularioRepositoryInterface;
use App\Domain\Formularios\Exceptions\FormularioNotFoundException;
use App\Infrastructure\Formularios\Models\Formulario;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentFormularioRepository implements FormularioRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array
    {
        $q = Formulario::query()->whereNull('deleted_at');

        if ($pagination->query) {
            $q->where('nombre', 'like', "%{$pagination->query}%");
        }

        $paginated = $q->orderBy('nombre')
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($f) => FormularioDTO::fromModel($f))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): FormularioDTO
    {
        $m = Formulario::whereNull('deleted_at')->find($id);
        if (! $m) {
            throw new FormularioNotFoundException($id);
        }

        return FormularioDTO::fromModel($m);
    }

    public function findBySlug(string $slug): FormularioDTO
    {
        $m = Formulario::where('slug', $slug)->whereNull('deleted_at')->first();
        if (! $m) {
            throw new FormularioNotFoundException($slug);
        }

        return FormularioDTO::fromModel($m);
    }

    public function findAllActivos(): array
    {
        return Formulario::where('activo', true)
            ->whereNull('deleted_at')
            ->orderBy('nombre')
            ->get()
            ->map(fn ($f) => FormularioDTO::fromModel($f))
            ->all();
    }

    public function create(array $data): FormularioDTO
    {
        $m = Formulario::create($data);

        return FormularioDTO::fromModel($m);
    }

    public function update(int $id, array $data): FormularioDTO
    {
        $m = Formulario::whereNull('deleted_at')->find($id);
        if (! $m) {
            throw new FormularioNotFoundException($id);
        }

        $m->update($data);

        return FormularioDTO::fromModel($m->fresh());
    }

    public function delete(int $id): bool
    {
        $m = Formulario::whereNull('deleted_at')->find($id);
        if (! $m) {
            throw new FormularioNotFoundException($id);
        }

        return (bool) $m->delete();
    }
}
