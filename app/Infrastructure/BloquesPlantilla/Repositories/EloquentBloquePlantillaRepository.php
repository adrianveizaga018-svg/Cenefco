<?php
namespace App\Infrastructure\BloquesPlantilla\Repositories;
use App\Application\BloquesPlantilla\DTOs\BloquePlantillaDTO;
use App\Domain\BloquesPlantilla\Contracts\BloquePlantillaRepositoryInterface;
use App\Domain\BloquesPlantilla\Exceptions\BloquePlantillaNotFoundException;
use App\Infrastructure\BloquesPlantilla\Models\BloquePlantilla;
use App\Shared\Kernel\DTOs\PaginationDTO;
class EloquentBloquePlantillaRepository implements BloquePlantillaRepositoryInterface {
    public function paginate(PaginationDTO $pagination, bool $conInactivos): array {
        $q = BloquePlantilla::query();
        if (!$conInactivos) $q->where('estado', 1);
        $paginated = $q->orderBy('id_bloqueplantilla', 'desc')->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);
        return ['data' => collect($paginated->items())->map(fn($r) => BloquePlantillaDTO::fromRow($r))->all(), 'total' => $paginated->total()];
    }
    public function findById(int $id): object {
        $row = BloquePlantilla::find($id);
        if (!$row) throw new BloquePlantillaNotFoundException($id);
        return $row;
    }
    public function create(array $data): object { return BloquePlantilla::create($data); }
    public function update(int $id, array $data): void { $this->findById($id)->update($data); }
    public function delete(int $id): void { $this->findById($id)->update(['estado' => 0]); }
}
