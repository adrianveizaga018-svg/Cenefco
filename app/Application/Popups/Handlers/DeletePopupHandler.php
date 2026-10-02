<?php
namespace App\Application\Popups\Handlers;
use App\Application\Popups\Commands\DeletePopupCommand;
use App\Domain\Popups\Contracts\PopupRepositoryInterface;
class DeletePopupHandler {
    public function __construct(private readonly PopupRepositoryInterface $repository) {}
    public function handle(DeletePopupCommand $c): void { $this->repository->delete($c->id); }
}
