<?php

declare(strict_types=1);

namespace App\Application\Descargables\Handlers;

use App\Application\Descargables\Commands\DeleteDescargableCommand;
use App\Domain\Descargables\Contracts\DescargableRepositoryInterface;

class DeleteDescargableHandler
{
    public function __construct(
        private readonly DescargableRepositoryInterface $repository,
    ) {}

    public function handle(DeleteDescargableCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
