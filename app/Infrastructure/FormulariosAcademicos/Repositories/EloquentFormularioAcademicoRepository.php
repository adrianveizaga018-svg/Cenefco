<?php
namespace App\Infrastructure\FormulariosAcademicos\Repositories;
use App\Application\FormulariosAcademicos\DTOs\FormularioAcademicoDTO;
use App\Domain\FormulariosAcademicos\Contracts\FormularioAcademicoRepositoryInterface;
use App\Domain\FormulariosAcademicos\Exceptions\FormularioAcademicoNotFoundException;
use App\Infrastructure\FormulariosAcademicos\Models\FormularioAcademico;
use App\Shared\Kernel\DTOs\PaginationDTO;
class EloquentFormularioAcademicoRepository implements FormularioAcademicoRepositoryInterface {
    public function paginate(PaginationDTO $pagination, bool $conInactivos): array {
        $q = FormularioAcademico::query();
        if (!$conInactivos) $q->where('estado', 1);
        $paginated = $q->orderBy('id_formulario', 'desc')->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);
        return ['data' => collect($paginated->items())->map(fn($r) => FormularioAcademicoDTO::fromRow($r))->all(), 'total' => $paginated->total()];
    }
    public function findById(int $id): object {
        $row = FormularioAcademico::find($id);
        if (!$row) throw new FormularioAcademicoNotFoundException($id);
        return $row;
    }
    public function create(array $data): object { return FormularioAcademico::create($data); }
    public function update(int $id, array $data): void { $this->findById($id)->update($data); }
    public function delete(int $id): void { $this->findById($id)->update(['estado' => 0]); }
}
