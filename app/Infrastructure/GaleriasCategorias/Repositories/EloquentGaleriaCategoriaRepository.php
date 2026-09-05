<?php
namespace App\Infrastructure\GaleriasCategorias\Repositories;
use App\Application\GaleriasCategorias\DTOs\GaleriaCategoriaDTO;
use App\Domain\GaleriasCategorias\Contracts\GaleriaCategoriaRepositoryInterface;
use App\Domain\GaleriasCategorias\Exceptions\GaleriaCategoriaNotFoundException;
use App\Infrastructure\GaleriasCategorias\Models\GaleriaCategoria;
use App\Shared\Kernel\DTOs\PaginationDTO;
class EloquentGaleriaCategoriaRepository implements GaleriaCategoriaRepositoryInterface {
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array {
        $q = GaleriaCategoria::query();
        if ($soloActivos) {
            $q->where('activo', true);
        }
        $paginated = $q->orderBy('orden')->orderByDesc('id')->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);
        return [
            'data' => collect($paginated->items())->map(fn($m) => GaleriaCategoriaDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }
    public function findById(int $id): GaleriaCategoriaDTO {
        $m = GaleriaCategoria::find($id);
        if (!$m) throw new GaleriaCategoriaNotFoundException($id);
        return GaleriaCategoriaDTO::fromModel($m);
    }
    public function findBySlug(string $slug): GaleriaCategoriaDTO {
        $m = GaleriaCategoria::where('slug', $slug)->first();
        if (!$m) throw new GaleriaCategoriaNotFoundException($slug);
        return GaleriaCategoriaDTO::fromModel($m);
    }
    public function create(array $data): GaleriaCategoriaDTO { return GaleriaCategoriaDTO::fromModel(GaleriaCategoria::create($data)); }
    public function update(int $id, array $data): GaleriaCategoriaDTO {
        $m = GaleriaCategoria::find($id);
        if (!$m) throw new GaleriaCategoriaNotFoundException($id);
        $m->update($data);
        return GaleriaCategoriaDTO::fromModel($m);
    }
    public function delete(int|array $ids): bool { return GaleriaCategoria::destroy($ids) > 0; }
}
