<?php

namespace App\Application\CertConfigProgramas\Handlers;

use App\Application\CertConfigProgramas\Commands\UpsertCertConfigCommand;
use App\Application\CertConfigProgramas\DTOs\CertConfigProgramaDTO;
use App\Application\CertConfigProgramas\QueryHandlers\GetCertConfigByProgramaQueryHandler;
use App\Application\CertConfigProgramas\Queries\GetCertConfigByProgramaQuery;
use App\Domain\CertConfigProgramas\Contracts\CertConfigProgramaRepositoryInterface;
use App\Domain\CertConfigProgramas\Contracts\CertConfigItemRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UpsertCertConfigHandler
{
    public function __construct(
        private readonly CertConfigProgramaRepositoryInterface $repo,
        private readonly CertConfigItemRepositoryInterface $itemRepo,
        private readonly GetCertConfigByProgramaQueryHandler $getHandler,
    ) {}

    public function handle(UpsertCertConfigCommand $command): CertConfigProgramaDTO
    {
        $existing = DB::table('web_cert_config_programa')
            ->where('programa_id', $command->programa_id)
            ->first();

        if ($existing) {
            $row = $this->repo->update($existing->id, [
                'activo'      => $command->activo,
                'titulo'      => $command->titulo,
                'descripcion' => $command->descripcion,
                'updated_at'  => now(),
            ]);
        } else {
            $row = $this->repo->create([
                'programa_id' => $command->programa_id,
                'activo'      => $command->activo,
                'titulo'      => $command->titulo,
                'descripcion' => $command->descripcion,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        $items = $this->itemRepo->findByConfigId($row->id);

        return CertConfigProgramaDTO::fromRow($row, $items);
    }
}
