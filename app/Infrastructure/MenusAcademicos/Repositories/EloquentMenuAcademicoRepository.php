<?php
namespace App\Infrastructure\MenusAcademicos\Repositories;
use App\Application\MenusAcademicos\DTOs\MenuAcademicoDTO;
use App\Domain\MenusAcademicos\Contracts\MenuAcademicoRepositoryInterface;
use App\Domain\MenusAcademicos\Exceptions\MenuAcademicoNotFoundException;
use App\Infrastructure\MenusAcademicos\Models\MenuAcademico;
use App\Shared\Kernel\DTOs\PaginationDTO;
class EloquentMenuAcademicoRepository implements MenuAcademicoRepositoryInterface {
    public function paginate(PaginationDTO $pagination, ?string $query, ?int $idMod, bool $conInactivos): array {
        $q = MenuAcademico::query();
        if (!$conInactivos) $q->where('estado', 1);
        if ($query) $q->where('nombre', 'like', "%{$query}%");
        if ($idMod !== null) $q->where('id_mod', $idMod);
        $paginated = $q->orderBy('id_men', 'desc')->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);
        return ['data' => collect($paginated->items())->map(fn($r) => MenuAcademicoDTO::fromRow($r))->all(), 'total' => $paginated->total()];
    }
    public function findById(int $id): object {
        $row = MenuAcademico::find($id);
        if (!$row) throw new MenuAcademicoNotFoundException($id);
        return $row;
    }
    public function create(array $data): object { return MenuAcademico::create($data); }
    public function update(int $id, array $data): void { $this->findById($id)->update($data); }
    public function delete(int $id): void { $this->findById($id)->update(['estado' => 0]); }
}
