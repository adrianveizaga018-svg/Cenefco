<?php
namespace App\Application\Popups\QueryHandlers;
use App\Application\Popups\Queries\GetPopupsQuery;
use App\Domain\Popups\Contracts\PopupRepositoryInterface;
class GetPopupsQueryHandler {
    public function __construct(private readonly PopupRepositoryInterface $repository) {}
    public function handle(GetPopupsQuery $q): array { return $this->repository->paginate($q->pagination); }
}
