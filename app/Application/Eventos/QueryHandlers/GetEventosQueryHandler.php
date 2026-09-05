<?php
namespace App\Application\Eventos\QueryHandlers;
use App\Application\Eventos\Queries\GetEventosQuery;
use App\Domain\Eventos\Contracts\EventoRepositoryInterface;
class GetEventosQueryHandler {
    public function __construct(private readonly EventoRepositoryInterface $repository) {}
    public function handle(GetEventosQuery $q): array { return $this->repository->paginate($q->pagination, $q->soloActivos); }
}
