<?php

declare(strict_types=1);

namespace App\Application\Descargables\Handlers;

use App\Application\Descargables\Commands\CreateDescargableCommand;
use App\Application\Descargables\DTOs\DescargableDTO;
use App\Domain\Descargables\Contracts\DescargableRepositoryInterface;

class CreateDescargableHandler
{
    public function __construct(
        private readonly DescargableRepositoryInterface $repository,
    ) {}

    public function handle(CreateDescargableCommand $command): DescargableDTO
    {
        return $this->repository->create($command);
    }
}
