<?php

namespace App\Application\SueldosDocentes\Handlers;

use App\Application\SueldosDocentes\Commands\UpdateSueldoDocenteCommand;
use App\Application\SueldosDocentes\DTOs\SueldoDocenteDTO;
use App\Domain\SueldosDocentes\Contracts\SueldoDocenteRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class UpdateSueldoDocenteHandler
{
    public function __construct(
        private readonly SueldoDocenteRepositoryInterface $repository,
    ) {}

    public function handle(UpdateSueldoDocenteCommand $command): SueldoDocenteDTO
    {
        if ($command->archivo_pdf !== null && $command->archivo_pdf_anterior) {
            Storage::disk('public')->delete($command->archivo_pdf_anterior);
        }

        $data = array_filter([
            'id_us'       => $command->id_us,
            'id_imp'      => $command->id_imp,
            'concepto'    => $command->concepto,
            'periodo'     => $command->periodo,
            'gestion'     => $command->gestion,
            'monto_total' => $command->monto_total,
            'observacion' => $command->observacion,
            'archivo_pdf' => $command->archivo_pdf,
        ], fn ($v) => $v !== null);

        return $this->repository->update($command->id, $data);
    }
}
