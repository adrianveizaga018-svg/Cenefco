<?php
namespace App\Application\WhatsappGrupos\QueryHandlers;
use App\Application\WhatsappGrupos\DTOs\WhatsappGrupoDTO;
use App\Application\WhatsappGrupos\Queries\GetWhatsappGrupoByIdQuery;
use App\Domain\WhatsappGrupos\Contracts\WhatsappGrupoRepositoryInterface;
class GetWhatsappGrupoByIdQueryHandler {
    public function __construct(private readonly WhatsappGrupoRepositoryInterface $repository) {}
    public function handle(GetWhatsappGrupoByIdQuery $q): WhatsappGrupoDTO { return $this->repository->findById($q->id); }
}
