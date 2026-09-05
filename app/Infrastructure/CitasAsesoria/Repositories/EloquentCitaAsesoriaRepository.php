<?php
namespace App\Infrastructure\CitasAsesoria\Repositories;
use App\Application\CitasAsesoria\DTOs\CitaAsesoriaDTO;
use App\Domain\CitasAsesoria\Contracts\CitaAsesoriaRepositoryInterface;
use App\Domain\CitasAsesoria\Exceptions\CitaAsesoriaNotFoundException;
use App\Infrastructure\CitasAsesoria\Models\CitaAsesoria;
use App\Shared\Kernel\DTOs\PaginationDTO;
class EloquentCitaAsesoriaRepository implements CitaAsesoriaRepositoryInterface {
    public function paginate(PaginationDTO $pagination, ?string $query, ?string $estado): array {
        $q = CitaAsesoria::query();
        if ($query) $q->where(function($sq) use ($query) {
            $sq->where('nombre', 'like', "%{$query}%")->orWhere('email', 'like', "%{$query}%");
        });
        if ($estado !== null) $q->where('estado', $estado);
        $paginated = $q->orderBy('id_cita_asesoria', 'desc')->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);
        return ['data' => collect($paginated->items())->map(fn($r) => CitaAsesoriaDTO::fromRow($r))->all(), 'total' => $paginated->total()];
    }
    public function findById(int $id): object {
        $row = CitaAsesoria::find($id);
        if (!$row) throw new CitaAsesoriaNotFoundException($id);
        return $row;
    }
    public function create(array $data): object { return CitaAsesoria::create($data); }
    public function update(int $id, array $data): void { $this->findById($id)->update($data); }
    public function delete(int $id): void { $this->findById($id)->delete(); }
}
