<?php
namespace App\Infrastructure\FormulariosIns\Repositories;
use App\Application\FormulariosIns\DTOs\FormularioInsDTO;
use App\Domain\FormulariosIns\Contracts\FormularioInsRepositoryInterface;
use App\Domain\FormulariosIns\Exceptions\FormularioInsNotFoundException;
use App\Infrastructure\FormulariosIns\Models\FormularioIns;
use App\Shared\Kernel\DTOs\PaginationDTO;
class EloquentFormularioInsRepository implements FormularioInsRepositoryInterface {
    public function paginate(PaginationDTO $pagination, ?string $query, ?int $idImp, bool $conInactivos): array {
        $q = FormularioIns::query();
        if (!$conInactivos) $q->where('estado', 1);
        if ($query) $q->where('nombre', 'like', "%{$query}%");
        if ($idImp !== null) $q->where('id_imp', $idImp);
        $paginated = $q->orderBy('id_formins', 'desc')->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);
        return ['data' => collect($paginated->items())->map(fn($r) => FormularioInsDTO::fromRow($r))->all(), 'total' => $paginated->total()];
    }
    public function findById(int $id): object {
        $row = FormularioIns::find($id);
        if (!$row) throw new FormularioInsNotFoundException($id);
        return $row;
    }
    public function create(array $data): object { return FormularioIns::create($data); }
    public function update(int $id, array $data): void { $this->findById($id)->update($data); }
    public function delete(int $id): void { $this->findById($id)->update(['estado' => 0]); }
}
