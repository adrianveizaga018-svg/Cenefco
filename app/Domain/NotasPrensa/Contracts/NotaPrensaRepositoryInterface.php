<?php

declare(strict_types=1);

namespace App\Domain\NotasPrensa\Contracts;

use App\Application\NotasPrensa\Commands\CreateNotaPrensaCommand;
use App\Application\NotasPrensa\Commands\UpdateNotaPrensaCommand;
use App\Application\NotasPrensa\DTOs\NotaPrensaDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Pagination\LengthAwarePaginator;

interface NotaPrensaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloDestacadas = false, bool $soloActivos = false): LengthAwarePaginator;

    public function findById(int|string $id): NotaPrensaDTO;

    public function create(CreateNotaPrensaCommand $command): NotaPrensaDTO;

    public function update(UpdateNotaPrensaCommand $command): NotaPrensaDTO;

    public function delete(int|string $id): void;
}
