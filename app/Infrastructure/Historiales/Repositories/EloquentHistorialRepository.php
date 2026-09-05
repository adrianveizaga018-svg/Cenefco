<?php
namespace App\Infrastructure\Historiales\Repositories;
use App\Application\Historiales\DTOs\HistorialDTO;
use App\Domain\Historiales\Contracts\HistorialRepositoryInterface;
use App\Domain\Historiales\Exceptions\HistorialNotFoundException;
use App\Infrastructure\Historiales\Models\Historial;
use App\Shared\Kernel\DTOs\PaginationDTO;
class EloquentHistorialRepository implements HistorialRepositoryInterface {
    public function paginate(PaginationDTO $pagination, ?int $idUs, ?int $idTiporeferencia, ?int $idTipohistorial, bool $conInactivos): array {
        $q = Historial::query();
        if (!$conInactivos) $q->where('estado', 1);
        if ($idUs !== null) $q->where('id_us', $idUs);
        if ($idTiporeferencia !== null) $q->where('id_tiporeferencia', $idTiporeferencia);
        if ($idTipohistorial !== null) $q->where('id_tipohistorial', $idTipohistorial);
        $paginated = $q->orderBy('id_historial', 'desc')->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);
        return ['data' => collect($paginated->items())->map(fn($r) => HistorialDTO::fromRow($r))->all(), 'total' => $paginated->total()];
    }
    public function findById(int $id): object {
        $row = Historial::find($id);
        if (!$row) throw new HistorialNotFoundException($id);
        return $row;
    }
    public function create(array $data): object { return Historial::create($data); }
    public function update(int $id, array $data): void { $this->findById($id)->update($data); }
    public function delete(int $id): void { $this->findById($id)->update(['estado' => 0]); }
}
