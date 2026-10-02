<?php
namespace App\Infrastructure\GruposAcademicos\Repositories;
use App\Application\GruposAcademicos\DTOs\GrupoAcademicoDTO;
use App\Domain\GruposAcademicos\Contracts\GrupoAcademicoRepositoryInterface;
use App\Domain\GruposAcademicos\Exceptions\GrupoAcademicoNotFoundException;
use App\Infrastructure\GruposAcademicos\Models\GrupoAcademico;
use App\Shared\Kernel\DTOs\PaginationDTO;
class EloquentGrupoAcademicoRepository implements GrupoAcademicoRepositoryInterface {
    public function paginate(PaginationDTO $pagination, ?string $query, ?int $idTest, bool $conInactivos): array {
        $q = GrupoAcademico::query();
        if (!$conInactivos) $q->where('estado', 1);
        if ($query) $q->where('nombre', 'like', "%{$query}%");
        if ($idTest !== null) $q->where('id_test', $idTest);
        $paginated = $q->orderBy('id_grupo', 'desc')->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);
        return ['data' => collect($paginated->items())->map(fn($r) => GrupoAcademicoDTO::fromRow($r))->all(), 'total' => $paginated->total()];
    }
    public function findById(int $id): object {
        $row = GrupoAcademico::find($id);
        if (!$row) throw new GrupoAcademicoNotFoundException($id);
        return $row;
    }
    public function create(array $data): object { return GrupoAcademico::create($data); }
    public function update(int $id, array $data): void { $this->findById($id)->update($data); }
    public function delete(int $id): void { $this->findById($id)->update(['estado' => 0]); }
}
