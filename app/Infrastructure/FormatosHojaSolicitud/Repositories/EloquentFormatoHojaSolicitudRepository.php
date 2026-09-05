<?php
namespace App\Infrastructure\FormatosHojaSolicitud\Repositories;
use App\Application\FormatosHojaSolicitud\DTOs\FormatoHojaSolicitudDTO;
use App\Domain\FormatosHojaSolicitud\Contracts\FormatoHojaSolicitudRepositoryInterface;
use App\Domain\FormatosHojaSolicitud\Exceptions\FormatoHojaSolicitudNotFoundException;
use App\Infrastructure\FormatosHojaSolicitud\Models\FormatoHojaSolicitud;
use App\Shared\Kernel\DTOs\PaginationDTO;
class EloquentFormatoHojaSolicitudRepository implements FormatoHojaSolicitudRepositoryInterface {
    public function paginate(PaginationDTO $pagination, bool $conInactivos): array {
        $q = FormatoHojaSolicitud::query();
        if (!$conInactivos) $q->where('estado', 1);
        $paginated = $q->orderBy('id_formato_hoja_solicitud', 'desc')->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);
        return ['data' => collect($paginated->items())->map(fn($r) => FormatoHojaSolicitudDTO::fromRow($r))->all(), 'total' => $paginated->total()];
    }
    public function findById(int $id): object {
        $row = FormatoHojaSolicitud::find($id);
        if (!$row) throw new FormatoHojaSolicitudNotFoundException($id);
        return $row;
    }
    public function create(array $data): object { return FormatoHojaSolicitud::create($data); }
    public function update(int $id, array $data): void { $this->findById($id)->update($data); }
    public function delete(int $id): void { $this->findById($id)->update(['estado' => 0]); }
}
