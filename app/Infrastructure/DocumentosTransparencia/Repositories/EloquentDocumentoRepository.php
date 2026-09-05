<?php

namespace App\Infrastructure\DocumentosTransparencia\Repositories;

use App\Application\DocumentosTransparencia\DTOs\DocumentoDTO;
use App\Domain\DocumentosTransparencia\Contracts\DocumentoRepositoryInterface;
use App\Domain\DocumentosTransparencia\Exceptions\DocumentoNotFoundException;
use App\Infrastructure\DocumentosTransparencia\Models\Documento;
use App\Shared\Kernel\DTOs\PaginationDTO;

class EloquentDocumentoRepository implements DocumentoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array
    {
        $q = Documento::query()->with(['tipoDocumento', 'secretaria']);

        if ($soloActivos) {
            $q->where('activo', true);
        }

        if ($pagination->query) {
            $q->where('titulo', 'like', "%{$pagination->query}%")
                ->orWhere('descripcion', 'like', "%{$pagination->query}%");
        }

        $paginated = $q->orderBy($pagination->sortKey, $pagination->sortOrder)
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data' => collect($paginated->items())->map(fn ($m) => DocumentoDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function findById(int $id): DocumentoDTO
    {
        $model = Documento::with(['tipoDocumento', 'secretaria'])->find($id);
        if (! $model) {
            throw new DocumentoNotFoundException($id);
        }

        return DocumentoDTO::fromModel($model);
    }

    public function findBySlug(string $slug): DocumentoDTO
    {
        $model = Documento::with(['tipoDocumento', 'secretaria'])->where('slug', $slug)->first();
        if (! $model) {
            throw new DocumentoNotFoundException($slug);
        }

        return DocumentoDTO::fromModel($model);
    }

    public function create(array $data): DocumentoDTO
    {
        $model = Documento::create($data);

        return DocumentoDTO::fromModel($model->load(['tipoDocumento', 'secretaria']));
    }

    public function update(int $id, array $data): DocumentoDTO
    {
        $model = Documento::find($id);
        if (! $model) {
            throw new DocumentoNotFoundException($id);
        }
        $model->update($data);

        return DocumentoDTO::fromModel($model->load(['tipoDocumento', 'secretaria']));
    }

    public function delete(int|array $ids): bool
    {
        return Documento::destroy($ids) > 0;
    }
}
