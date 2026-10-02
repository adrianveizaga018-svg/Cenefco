<?php

declare(strict_types=1);

namespace App\Domain\Suscriptores\Contracts;

use App\Application\Suscriptores\Commands\CreateSuscriptorCommand;
use App\Application\Suscriptores\Commands\UpdateSuscriptorCommand;
use App\Application\Suscriptores\DTOs\SuscriptorDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Pagination\LengthAwarePaginator;

interface SuscriptorRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): LengthAwarePaginator;

    public function findById(int|string $id): SuscriptorDTO;

    public function create(CreateSuscriptorCommand $command): SuscriptorDTO;

    public function update(UpdateSuscriptorCommand $command): SuscriptorDTO;

    public function delete(int|string $id): void;
}
