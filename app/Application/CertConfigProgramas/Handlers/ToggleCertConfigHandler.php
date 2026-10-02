<?php

namespace App\Application\CertConfigProgramas\Handlers;

use App\Application\CertConfigProgramas\Commands\ToggleCertConfigCommand;
use App\Application\CertConfigProgramas\DTOs\CertConfigProgramaDTO;
use App\Domain\CertConfigProgramas\Contracts\CertConfigProgramaRepositoryInterface;
use App\Domain\CertConfigProgramas\Contracts\CertConfigItemRepositoryInterface;
use App\Domain\CertConfigProgramas\Exceptions\CertConfigNotFoundException;

class ToggleCertConfigHandler
{
    public function __construct(
        private readonly CertConfigProgramaRepositoryInterface $repo,
        private readonly CertConfigItemRepositoryInterface $itemRepo,
    ) {}

    public function handle(ToggleCertConfigCommand $command): CertConfigProgramaDTO
    {
        $current = $this->repo->findById($command->id);
        if (! $current) {
            throw new CertConfigNotFoundException($command->id);
        }

        $row = $this->repo->update($command->id, [
            'activo'     => ! $current->activo,
            'updated_at' => now(),
        ]);

        $items = $this->itemRepo->findByConfigId($command->id);

        return CertConfigProgramaDTO::fromRow($row, $items);
    }
}
