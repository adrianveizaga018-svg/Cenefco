<?php
namespace App\Infrastructure\SeccionesBloque\Repositories;
use App\Application\SeccionesBloque\DTOs\SeccionBloqueDTO;
use App\Domain\SeccionesBloque\Contracts\SeccionBloqueRepositoryInterface;
use App\Domain\SeccionesBloque\Exceptions\SeccionBloqueNotFoundException;
use App\Infrastructure\SeccionesBloque\Models\SeccionBloque;
use App\Shared\Kernel\DTOs\PaginationDTO;
class EloquentSeccionBloqueRepository implements SeccionBloqueRepositoryInterface {
    public function paginate(PaginationDTO $pagination, ?int $idBloqueajustable, bool $conInactivos): array {
        $q = SeccionBloque::query();
        if (!$conInactivos) $q->where('estado', 1);
        if ($idBloqueajustable !== null) $q->where('id_bloqueajustable', $idBloqueajustable);
        $paginated = $q->orderBy('id_seccionbloque', 'desc')->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);
        return ['data' => collect($paginated->items())->map(fn($r) => SeccionBloqueDTO::fromRow($r))->all(), 'total' => $paginated->total()];
    }
    public function findById(int $id): object {
        $row = SeccionBloque::find($id);
        if (!$row) throw new SeccionBloqueNotFoundException($id);
        return $row;
    }
    public function create(array $data): object { return SeccionBloque::create($data); }
    public function update(int $id, array $data): void { $this->findById($id)->update($data); }
    public function delete(int $id): void { $this->findById($id)->update(['estado' => 0]); }
}
