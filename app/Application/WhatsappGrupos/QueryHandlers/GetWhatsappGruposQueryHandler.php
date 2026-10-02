<?php
namespace App\Application\WhatsappGrupos\QueryHandlers;
use App\Application\WhatsappGrupos\Queries\GetWhatsappGruposQuery;
use App\Domain\WhatsappGrupos\Contracts\WhatsappGrupoRepositoryInterface;
class GetWhatsappGruposQueryHandler {
    public function __construct(private readonly WhatsappGrupoRepositoryInterface $repository) {}
    public function handle(GetWhatsappGruposQuery $q): array {
        return $this->repository->paginate($q->pagination, $q->soloActivos);
    }
}
