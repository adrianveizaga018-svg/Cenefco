<?php
namespace App\Infrastructure\HojasEvaluacion\Repositories;
use App\Application\HojasEvaluacion\DTOs\HojaEvaluacionDTO;
use App\Domain\HojasEvaluacion\Contracts\HojaEvaluacionRepositoryInterface;
use App\Domain\HojasEvaluacion\Exceptions\HojaEvaluacionNotFoundException;
use App\Infrastructure\HojasEvaluacion\Models\HojaEvaluacion;
use App\Shared\Kernel\DTOs\PaginationDTO;
class EloquentHojaEvaluacionRepository implements HojaEvaluacionRepositoryInterface {
    public function paginate(PaginationDTO $pagination, ?int $idUs, ?int $idUsTut, bool $conInactivos): array {
        $q = HojaEvaluacion::query();
        if (!$conInactivos) $q->where('estado', 1);
        if ($idUs !== null) $q->where('id_us', $idUs);
        if ($idUsTut !== null) $q->where('id_us_tut', $idUsTut);
        $paginated = $q->orderBy('id_hojaevaluacion', 'desc')->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);
        return ['data' => collect($paginated->items())->map(fn($r) => HojaEvaluacionDTO::fromRow($r))->all(), 'total' => $paginated->total()];
    }
    public function findById(int $id): object {
        $row = HojaEvaluacion::find($id);
        if (!$row) throw new HojaEvaluacionNotFoundException($id);
        return $row;
    }
    public function create(array $data): object { return HojaEvaluacion::create($data); }
    public function update(int $id, array $data): void { $this->findById($id)->update($data); }
    public function delete(int $id): void { $this->findById($id)->update(['estado' => 0]); }
}
