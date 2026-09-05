<?php
namespace App\Application\WhatsappGrupos\Handlers;
use App\Application\WhatsappGrupos\Commands\DeleteWhatsappGrupoCommand;
use App\Domain\WhatsappGrupos\Contracts\WhatsappGrupoRepositoryInterface;
class DeleteWhatsappGrupoHandler {
    public function __construct(private readonly WhatsappGrupoRepositoryInterface $repository) {}
    public function handle(DeleteWhatsappGrupoCommand $c): void { $this->repository->delete($c->id); }
}
