<?php
namespace App\Infrastructure\BloquesAjustables\Repositories;
use App\Application\BloquesAjustables\DTOs\BloqueAjustableDTO;
use App\Domain\BloquesAjustables\Contracts\BloqueAjustableRepositoryInterface;
use App\Domain\BloquesAjustables\Exceptions\BloqueAjustableNotFoundException;
use App\Infrastructure\BloquesAjustables\Models\BloqueAjustable;
use App\Shared\Kernel\DTOs\PaginationDTO;
class EloquentBloqueAjustableRepository implements BloqueAjustableRepositoryInterface {
    public function paginate(PaginationDTO $pagination, ?int $idPagina, ?int $idBloqueplantilla, bool $conInactivos): array {
        $q = BloqueAjustable::query();
        if (!$conInactivos) $q->where('estado', 1);
        if ($idPagina !== null) $q->where('id_pagina', $idPagina);
        if ($idBloqueplantilla !== null) $q->where('id_bloqueplantilla', $idBloqueplantilla);
        $paginated = $q->orderBy('id_bloqueajustable', 'desc')->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);
        return ['data' => collect($paginated->items())->map(fn($r) => BloqueAjustableDTO::fromRow($r))->all(), 'total' => $paginated->total()];
    }
    public function findById(int $id): object {
        $row = BloqueAjustable::find($id);
        if (!$row) throw new BloqueAjustableNotFoundException($id);
        return $row;
    }
    public function create(array $data): object { return BloqueAjustable::create($data); }
    public function update(int $id, array $data): void { $this->findById($id)->update($data); }
    public function delete(int $id): void { $this->findById($id)->update(['estado' => 0]); }
}
