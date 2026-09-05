<?php

namespace App\Application\RedesSociales\Handlers;

use App\Application\RedesSociales\Commands\DeleteRedSocialCommand;
use App\Domain\RedesSociales\Contracts\RedSocialRepositoryInterface;
use App\Shared\Kernel\Contracts\CommandHandlerInterface;
use App\Shared\Kernel\Contracts\CommandInterface;

class DeleteRedSocialHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly RedSocialRepositoryInterface $repository,
    ) {}

    public function handle(CommandInterface $command): bool
    {
        
        return $this->repository->delete($command->ids);
    }
}
