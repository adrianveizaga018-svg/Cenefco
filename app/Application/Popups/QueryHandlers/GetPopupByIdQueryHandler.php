<?php
namespace App\Application\Popups\QueryHandlers;
use App\Application\Popups\DTOs\PopupDTO;
use App\Application\Popups\Queries\GetPopupByIdQuery;
use App\Domain\Popups\Contracts\PopupRepositoryInterface;
class GetPopupByIdQueryHandler {
    public function __construct(private readonly PopupRepositoryInterface $repository) {}
    public function handle(GetPopupByIdQuery $q): PopupDTO { return $this->repository->findById($q->id); }
}
