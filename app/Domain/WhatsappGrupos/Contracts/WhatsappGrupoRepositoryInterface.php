<?php
namespace App\Domain\WhatsappGrupos\Contracts;
use App\Application\WhatsappGrupos\DTOs\WhatsappGrupoDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;
interface WhatsappGrupoRepositoryInterface {
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array;
    public function findById(int $id): WhatsappGrupoDTO;
    public function create(array $data): WhatsappGrupoDTO;
    public function update(int $id, array $data): WhatsappGrupoDTO;
    public function delete(int|array $ids): bool;
}
