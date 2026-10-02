<?php

namespace App\Application\CertConfigProgramas\Handlers;

use App\Application\CertConfigProgramas\Commands\AprobarSolicitudCommand;
use App\Application\CertConfigProgramas\DTOs\CertSolicitudDTO;
use App\Domain\CertConfigProgramas\Contracts\CertConfigItemRepositoryInterface;
use App\Domain\CertConfigProgramas\Contracts\CertSolicitudRepositoryInterface;
use App\Domain\CertConfigProgramas\Exceptions\CertSolicitudNotFoundException;
use App\Application\Certificados\Services\CertificadoService;
use Illuminate\Support\Facades\DB;

class AprobarSolicitudHandler
{
    public function __construct(
        private readonly CertSolicitudRepositoryInterface    $repo,
        private readonly CertConfigItemRepositoryInterface   $itemRepo,
        private readonly CertificadoService                  $certService,
    ) {}

    public function handle(AprobarSolicitudCommand $command): CertSolicitudDTO
    {
        $solicitud = $this->repo->findById($command->solicitud_id);
        if (! $solicitud) {
            throw new CertSolicitudNotFoundException($command->solicitud_id);
        }

        if ($solicitud->estado !== 'pendiente_revision') {
            abort(422, 'Solo se pueden aprobar solicitudes en estado pendiente_revision.');
        }

        return DB::transaction(function () use ($command, $solicitud) {
            $certId = null;

            $item = $this->itemRepo->findById($solicitud->config_item_id);
            if ($item && $item->plantilla_id) {
                $cert   = $this->certService->generarCertificadoDirecto(
                    plantillaId:    (int) $item->plantilla_id,
                    nombreCompleto: $solicitud->usuario_nombre,
                    nombrePrograma: $item->nombre_cert,
                    ci:             $solicitud->usuario_ci,
                );
                $certId = $cert->id;
            }

            $row = $this->repo->update($command->solicitud_id, [
                'estado'         => 'generado',
                'certificado_id' => $certId,
                'updated_at'     => now(),
            ]);

            return CertSolicitudDTO::fromRow($row);
        });
    }
}
