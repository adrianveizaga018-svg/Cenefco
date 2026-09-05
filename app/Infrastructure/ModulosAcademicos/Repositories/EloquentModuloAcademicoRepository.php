<?php
namespace App\Infrastructure\ModulosAcademicos\Repositories;
use App\Application\ModulosAcademicos\DTOs\ModuloAcademicoDTO;
use App\Domain\ModulosAcademicos\Contracts\ModuloAcademicoRepositoryInterface;
use App\Domain\ModulosAcademicos\Exceptions\ModuloAcademicoNotFoundException;
use App\Infrastructure\ModulosAcademicos\Models\ModuloAcademico;
use App\Shared\Kernel\DTOs\PaginationDTO;
class EloquentModuloAcademicoRepository implements ModuloAcademicoRepositoryInterface {
    public function paginate(PaginationDTO $pagination, ?string $query, ?int $posicion, bool $conInactivos): array {
        $q = ModuloAcademico::query();
        if (!$conInactivos) $q->where('estado', 1);
        if ($query) $q->where('nombre', 'like', "%{$query}%");
        if ($posicion !== null) $q->where('posicion', $posicion);
        $paginated = $q->orderBy('posicion')->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);
        return ['data' => collect($paginated->items())->map(fn($r) => ModuloAcademicoDTO::fromRow($r))->all(), 'total' => $paginated->total()];
    }
    public function findById(int $id): object {
        $row = ModuloAcademico::find($id);
        if (!$row) throw new ModuloAcademicoNotFoundException($id);
        return $row;
    }
    public function create(array $data): object { return ModuloAcademico::create($data); }
    public function update(int $id, array $data): void { $this->findById($id)->update($data); }
    public function delete(int $id): void { $this->findById($id)->update(['estado' => 0]); }
}
