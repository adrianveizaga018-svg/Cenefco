<?php
namespace App\Domain\Popups\Contracts;
use App\Application\Popups\DTOs\PopupDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;
interface PopupRepositoryInterface {
    public function paginate(PaginationDTO $pagination): array;
    public function findById(int $id): PopupDTO;
    public function create(array $data): PopupDTO;
    public function update(int $id, array $data): PopupDTO;
    public function delete(int|array $ids): bool;
}
