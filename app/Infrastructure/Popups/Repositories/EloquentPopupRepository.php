<?php
namespace App\Infrastructure\Popups\Repositories;
use App\Application\Popups\DTOs\PopupDTO;
use App\Domain\Popups\Contracts\PopupRepositoryInterface;
use App\Domain\Popups\Exceptions\PopupNotFoundException;
use App\Infrastructure\Popups\Models\Popup;
use App\Shared\Kernel\DTOs\PaginationDTO;
class EloquentPopupRepository implements PopupRepositoryInterface {
    public function paginate(PaginationDTO $pagination): array {
        $q = Popup::query();
        $paginated = $q->orderByDesc('id')->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);
        return ['data' => collect($paginated->items())->map(fn($m) => PopupDTO::fromModel($m))->all(), 'total' => $paginated->total()];
    }
    public function findById(int $id): PopupDTO {
        $m = Popup::find($id);
        if (!$m) throw new PopupNotFoundException($id);
        return PopupDTO::fromModel($m);
    }
    public function create(array $data): PopupDTO { return PopupDTO::fromModel(Popup::create($data)); }
    public function update(int $id, array $data): PopupDTO {
        $m = Popup::find($id);
        if (!$m) throw new PopupNotFoundException($id);
        $m->update($data);
        return PopupDTO::fromModel($m);
    }
    public function delete(int|array $ids): bool { return Popup::destroy($ids) > 0; }
}
