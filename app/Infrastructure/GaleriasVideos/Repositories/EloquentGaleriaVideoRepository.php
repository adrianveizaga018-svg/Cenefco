<?php
namespace App\Infrastructure\GaleriasVideos\Repositories;
use App\Application\GaleriasVideos\DTOs\GaleriaVideoDTO;
use App\Domain\GaleriasVideos\Contracts\GaleriaVideoRepositoryInterface;
use App\Domain\GaleriasVideos\Exceptions\GaleriaVideoNotFoundException;
use App\Infrastructure\GaleriasVideos\Models\GaleriaVideo;
use App\Shared\Kernel\DTOs\PaginationDTO;
class EloquentGaleriaVideoRepository implements GaleriaVideoRepositoryInterface {
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false, bool $soloDestacados = false): array {
        $q = GaleriaVideo::query();
        if ($soloActivos) {
            $q->where('activo', true);
        }
        if ($soloDestacados) {
            $q->where('destacado', true);
        }
        $paginated = $q->orderBy('orden')->orderByDesc('id')->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);
        return [
            'data' => collect($paginated->items())->map(fn($m) => GaleriaVideoDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }
    public function findById(int $id): GaleriaVideoDTO {
        $m = GaleriaVideo::find($id);
        if (!$m) throw new GaleriaVideoNotFoundException($id);
        return GaleriaVideoDTO::fromModel($m);
    }
    public function create(array $data): GaleriaVideoDTO { return GaleriaVideoDTO::fromModel(GaleriaVideo::create($data)); }
    public function update(int $id, array $data): GaleriaVideoDTO {
        $m = GaleriaVideo::find($id);
        if (!$m) throw new GaleriaVideoNotFoundException($id);
        $m->update($data);
        return GaleriaVideoDTO::fromModel($m);
    }
    public function delete(int|array $ids): bool { return GaleriaVideo::destroy($ids) > 0; }
}
