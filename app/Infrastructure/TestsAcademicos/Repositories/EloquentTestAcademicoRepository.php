<?php
namespace App\Infrastructure\TestsAcademicos\Repositories;
use App\Application\TestsAcademicos\DTOs\TestAcademicoDTO;
use App\Domain\TestsAcademicos\Contracts\TestAcademicoRepositoryInterface;
use App\Domain\TestsAcademicos\Exceptions\TestAcademicoNotFoundException;
use App\Infrastructure\TestsAcademicos\Models\TestAcademico;
use App\Shared\Kernel\DTOs\PaginationDTO;
class EloquentTestAcademicoRepository implements TestAcademicoRepositoryInterface {
    public function paginate(PaginationDTO $pagination, ?string $query, ?bool $habilitarTest, bool $conInactivos): array {
        $q = TestAcademico::query();
        if (!$conInactivos) $q->where('estado', 1);
        if ($query) $q->where('nombre', 'like', "%{$query}%");
        if ($habilitarTest !== null) $q->where('habilitar_test', $habilitarTest);
        $paginated = $q->orderBy('id_test', 'desc')->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);
        return ['data' => collect($paginated->items())->map(fn($r) => TestAcademicoDTO::fromRow($r))->all(), 'total' => $paginated->total()];
    }
    public function findById(int $id): object {
        $row = TestAcademico::find($id);
        if (!$row) throw new TestAcademicoNotFoundException($id);
        return $row;
    }
    public function create(array $data): object { return TestAcademico::create($data); }
    public function update(int $id, array $data): void { $this->findById($id)->update($data); }
    public function delete(int $id): void { $this->findById($id)->update(['estado' => 0]); }
}
