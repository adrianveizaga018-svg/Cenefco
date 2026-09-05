<?php
namespace App\Infrastructure\ArchivosAcademicos\Repositories;
use App\Application\ArchivosAcademicos\DTOs\ArchivoAcademicoDTO;
use App\Domain\ArchivosAcademicos\Contracts\ArchivoAcademicoRepositoryInterface;
use App\Domain\ArchivosAcademicos\Exceptions\ArchivoAcademicoNotFoundException;
use App\Infrastructure\ArchivosAcademicos\Models\ArchivoAcademico;
use App\Shared\Kernel\DTOs\PaginationDTO;
class EloquentArchivoAcademicoRepository implements ArchivoAcademicoRepositoryInterface {
    public function paginate(PaginationDTO $pagination, bool $conInactivos): array {
        $q = ArchivoAcademico::query();
        if (!$conInactivos) $q->where('estado', 1);
        $paginated = $q->orderBy('id_arch', 'desc')->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);
        return ['data' => collect($paginated->items())->map(fn($r) => ArchivoAcademicoDTO::fromRow($r))->all(), 'total' => $paginated->total()];
    }
    public function findById(int $id): object {
        $row = ArchivoAcademico::find($id);
        if (!$row) throw new ArchivoAcademicoNotFoundException($id);
        return $row;
    }
    public function create(array $data): object { return ArchivoAcademico::create($data); }
    public function update(int $id, array $data): void { $this->findById($id)->update($data); }
    public function delete(int $id): void { $this->findById($id)->update(['estado' => 0]); }
}
