<?php
namespace App\Infrastructure\WebMenus\Repositories;
use App\Application\WebMenus\DTOs\WebMenuDTO;
use App\Domain\WebMenus\Contracts\WebMenuRepositoryInterface;
use App\Domain\WebMenus\Exceptions\WebMenuNotFoundException;
use App\Infrastructure\WebMenus\Models\WebMenu;
use App\Shared\Kernel\DTOs\PaginationDTO;
class EloquentWebMenuRepository implements WebMenuRepositoryInterface {
    public function paginate(PaginationDTO $pagination, ?string $query): array {
        $q = WebMenu::query();
        if ($query) $q->where('nombre', 'like', "%{$query}%");
        $paginated = $q->orderBy('nombre')->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);
        return ['data' => collect($paginated->items())->map(fn($r) => WebMenuDTO::fromRow($r))->all(), 'total' => $paginated->total()];
    }
    public function findById(int $id): object {
        $row = WebMenu::find($id);
        if (!$row) throw new WebMenuNotFoundException($id);
        return $row;
    }
    public function findByNombre(string $nombre): ?object {
        return WebMenu::where('nombre', $nombre)->first();
    }
    public function create(array $data): object { return WebMenu::create($data); }
    public function update(int $id, array $data): void { $this->findById($id)->update($data); }
    public function delete(int $id): void { $this->findById($id)->delete(); }
}
