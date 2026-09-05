<?php

namespace App\Infrastructure\TiposDocumentoTransparencia\Repositories;

use App\Application\TiposDocumentoTransparencia\DTOs\TipoDocumentoTransparenciaDTO;
use App\Domain\TiposDocumentoTransparencia\Contracts\TipoDocumentoTransparenciaRepositoryInterface;
use App\Domain\TiposDocumentoTransparencia\Exceptions\TipoDocumentoTransparenciaNotFoundException;
use App\Infrastructure\TiposDocumentoTransparencia\Models\TipoDocumentoTransparencia;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentTipoDocumentoTransparenciaRepository implements TipoDocumentoTransparenciaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array
    {
        $q = TipoDocumentoTransparencia::query();

        if ($pagination->query) {
            $q->where('nombre', 'like', "%{$pagination->query}%");
        }

        $paginated = $q->orderBy($pagination->sortKey, $pagination->sortOrder)
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data' => collect($paginated->items())->map(fn ($m) => TipoDocumentoTransparenciaDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): TipoDocumentoTransparenciaDTO
    {
        $model = TipoDocumentoTransparencia::find($id);
        if (! $model) {
            throw new TipoDocumentoTransparenciaNotFoundException($id);
        }

        return TipoDocumentoTransparenciaDTO::fromModel($model);
    }

    public function create(array $data): TipoDocumentoTransparenciaDTO
    {
        $model = TipoDocumentoTransparencia::create($data);

        return TipoDocumentoTransparenciaDTO::fromModel($model);
    }

    public function update(int $id, array $data): TipoDocumentoTransparenciaDTO
    {
        $model = TipoDocumentoTransparencia::find($id);
        if (! $model) {
            throw new TipoDocumentoTransparenciaNotFoundException($id);
        }
        $model->update($data);

        return TipoDocumentoTransparenciaDTO::fromModel($model);
    }

    public function delete(int|array $ids): bool
    {
        return TipoDocumentoTransparencia::destroy($ids) > 0;
    }
}
