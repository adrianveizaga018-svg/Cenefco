<?php
namespace App\Application\Eventos\QueryHandlers;
use App\Application\Eventos\DTOs\EventoDTO;
use App\Application\Eventos\Queries\GetEventoBySlugQuery;
use App\Domain\Eventos\Contracts\EventoRepositoryInterface;
class GetEventoBySlugQueryHandler {
    public function __construct(private readonly EventoRepositoryInterface $repository) {}
    public function handle(GetEventoBySlugQuery $q): EventoDTO { return $this->repository->findBySlug($q->slug); }
}
