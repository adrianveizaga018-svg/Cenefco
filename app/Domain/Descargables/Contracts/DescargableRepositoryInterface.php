<?php

declare(strict_types=1);

namespace App\Domain\Descargables\Contracts;

use App\Application\Descargables\Commands\CreateDescargableCommand;
use App\Application\Descargables\Commands\UpdateDescargableCommand;
use App\Application\Descargables\DTOs\DescargableDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Pagination\LengthAwarePaginator;

interface DescargableRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): LengthAwarePaginator;

    public function findById(int|string $id): DescargableDTO;

    public function create(CreateDescargableCommand $command): DescargableDTO;

    public function update(UpdateDescargableCommand $command): DescargableDTO;

    public function delete(int|string $id): void;
}
