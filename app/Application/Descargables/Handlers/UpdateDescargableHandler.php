<?php

declare(strict_types=1);

namespace App\Application\Descargables\Handlers;

use App\Application\Descargables\Commands\UpdateDescargableCommand;
use App\Application\Descargables\DTOs\DescargableDTO;
use App\Domain\Descargables\Contracts\DescargableRepositoryInterface;

class UpdateDescargableHandler
{
    public function __construct(
        private readonly DescargableRepositoryInterface $repository,
    ) {}

    public function handle(UpdateDescargableCommand $command): DescargableDTO
    {
        return $this->repository->update($command);
    }
}
