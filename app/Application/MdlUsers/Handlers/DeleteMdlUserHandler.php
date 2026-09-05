<?php

namespace App\Application\MdlUsers\Handlers;

use App\Application\MdlUsers\Commands\DeleteMdlUserCommand;
use App\Domain\MdlUsers\Contracts\MdlUserRepositoryInterface;

class DeleteMdlUserHandler
{
    public function __construct(private readonly MdlUserRepositoryInterface $repository) {}

    public function handle(DeleteMdlUserCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
