<?php
namespace App\Infrastructure\PaginasAcademicas\Repositories;
use App\Application\PaginasAcademicas\DTOs\PaginaAcademicaDTO;
use App\Domain\PaginasAcademicas\Contracts\PaginaAcademicaRepositoryInterface;
use App\Domain\PaginasAcademicas\Exceptions\PaginaAcademicaNotFoundException;
use App\Infrastructure\PaginasAcademicas\Models\PaginaAcademica;
use App\Shared\Kernel\DTOs\PaginationDTO;
class EloquentPaginaAcademicaRepository implements PaginaAcademicaRepositoryInterface {
    public function paginate(PaginationDTO $pagination, bool $conInactivos): array {
        $q = PaginaAcademica::query();
        if (!$conInactivos) $q->where('estado', 1);
        $paginated = $q->orderBy('id_pagina', 'desc')->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);
        return ['data' => collect($paginated->items())->map(fn($r) => PaginaAcademicaDTO::fromRow($r))->all(), 'total' => $paginated->total()];
    }
    public function findById(int $id): object {
        $row = PaginaAcademica::find($id);
        if (!$row) throw new PaginaAcademicaNotFoundException($id);
        return $row;
    }
    public function create(array $data): object { return PaginaAcademica::create($data); }
    public function update(int $id, array $data): void { $this->findById($id)->update($data); }
    public function delete(int $id): void { $this->findById($id)->update(['estado' => 0]); }
}
