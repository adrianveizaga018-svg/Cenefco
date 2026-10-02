<?php
namespace App\Infrastructure\WebMenuItems\Repositories;
use App\Application\WebMenuItems\DTOs\WebMenuItemDTO;
use App\Domain\WebMenuItems\Contracts\WebMenuItemRepositoryInterface;
use App\Domain\WebMenuItems\Exceptions\WebMenuItemNotFoundException;
use App\Infrastructure\WebMenuItems\Models\WebMenuItem;
use App\Shared\Kernel\DTOs\PaginationDTO;
class EloquentWebMenuItemRepository implements WebMenuItemRepositoryInterface {
    public function byMenu(int $menuId): array {
        $items = WebMenuItem::where('menu_id', $menuId)->orderBy('orden')->get();
        return collect($items)->map(fn($r) => WebMenuItemDTO::fromRow($r))->all();
    }
    public function findById(int $id): object {
        $row = WebMenuItem::find($id);
        if (!$row) throw new WebMenuItemNotFoundException($id);
        return $row;
    }
    public function create(array $data): object { return WebMenuItem::create($data); }
    public function update(int $id, array $data): void { $this->findById($id)->update($data); }
    public function delete(int $id): void { $this->findById($id)->delete(); }
}
