<?php
namespace App\Infrastructure\WhatsappGrupos\Repositories;
use App\Application\WhatsappGrupos\DTOs\WhatsappGrupoDTO;
use App\Domain\WhatsappGrupos\Contracts\WhatsappGrupoRepositoryInterface;
use App\Domain\WhatsappGrupos\Exceptions\WhatsappGrupoNotFoundException;
use App\Infrastructure\WhatsappGrupos\Models\WhatsappGrupo;
use App\Shared\Kernel\DTOs\PaginationDTO;
class EloquentWhatsappGrupoRepository implements WhatsappGrupoRepositoryInterface {
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array {
        $q = WhatsappGrupo::query();
        if ($soloActivos) {
            $q->where('activo', true);
        }
        $paginated = $q->orderBy('orden')->orderByDesc('id')->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);
        return [
            'data' => collect($paginated->items())->map(fn($m) => WhatsappGrupoDTO::fromModel($m))->all(),
            'total' => $paginated->total(),
        ];
    }
    public function findById(int $id): WhatsappGrupoDTO {
        $m = WhatsappGrupo::find($id);
        if (!$m) throw new WhatsappGrupoNotFoundException($id);
        return WhatsappGrupoDTO::fromModel($m);
    }
    public function create(array $data): WhatsappGrupoDTO { return WhatsappGrupoDTO::fromModel(WhatsappGrupo::create($data)); }
    public function update(int $id, array $data): WhatsappGrupoDTO {
        $m = WhatsappGrupo::find($id);
        if (!$m) throw new WhatsappGrupoNotFoundException($id);
        $m->update($data);
        return WhatsappGrupoDTO::fromModel($m);
    }
    public function delete(int|array $ids): bool { return WhatsappGrupo::destroy($ids) > 0; }
}
