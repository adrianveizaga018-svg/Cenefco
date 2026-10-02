<?php

namespace App\Application\CertConfigProgramas\Handlers;

use App\Application\CertConfigProgramas\Commands\CrearSolicitudesCommand;
use App\Application\CertConfigProgramas\DTOs\CertSolicitudDTO;
use App\Domain\CertConfigProgramas\Contracts\CertConfigProgramaRepositoryInterface;
use App\Domain\CertConfigProgramas\Contracts\CertConfigItemRepositoryInterface;
use App\Domain\CertConfigProgramas\Contracts\CertSolicitudRepositoryInterface;
use App\Domain\CertConfigProgramas\Exceptions\CertConfigNotFoundException;
use App\Application\Certificados\Services\CertificadoService;
use Illuminate\Support\Facades\DB;

class CrearSolicitudesHandler
{
    public function __construct(
        private readonly CertConfigProgramaRepositoryInterface $configRepo,
        private readonly CertConfigItemRepositoryInterface $itemRepo,
        private readonly CertSolicitudRepositoryInterface $solicitudRepo,
        private readonly CertificadoService $certService,
    ) {}

    public function handle(CrearSolicitudesCommand $command): array
    {
        $config = $this->configRepo->findByProgramaId($command->programa_id);
        if (! $config || ! $config->activo) {
            throw new CertConfigNotFoundException($command->programa_id);
        }

        $allItems = $this->itemRepo->findByConfigId($config->id);
        $gratuito = collect($allItems)->first(fn ($i) => $i->es_gratuito && $i->activo);

        return DB::transaction(function () use ($command, $config, $gratuito, $allItems) {
            $creadas = [];

            if ($gratuito) {
                $certId  = null;
                $certUrl = null;
                if ($gratuito->plantilla_id) {
                    try {
                        $cert    = $this->certService->generarCertificadoDirecto(
                            plantillaId:    $gratuito->plantilla_id,
                            nombreCompleto: $command->usuario_nombre,
                            nombrePrograma: $gratuito->nombre_cert,
                            ci:             $command->usuario_ci,
                        );
                        $certId  = $cert->id;
                        $certUrl = $cert->archivo_url;
                    } catch (\Throwable) {

                    }
                }

                $rows = $this->solicitudRepo->createMany([[
                    'config_item_id'  => $gratuito->id,
                    'inscripcion_id'  => $command->inscripcion_id,
                    'usuario_ci'      => $command->usuario_ci,
                    'usuario_nombre'  => $command->usuario_nombre,
                    'usuario_email'   => $command->usuario_email,
                    'es_gratuito'     => true,
                    'estado'          => $certId ? 'generado' : 'pendiente_pago',
                    'certificado_id'  => $certId,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]]);

                if ($certUrl) {
                    $rows[0] = new CertSolicitudDTO(
                        id:              $rows[0]->id,
                        config_item_id:  $rows[0]->config_item_id,
                        inscripcion_id:  $rows[0]->inscripcion_id,
                        usuario_ci:      $rows[0]->usuario_ci,
                        usuario_nombre:  $rows[0]->usuario_nombre,
                        usuario_email:   $rows[0]->usuario_email,
                        es_gratuito:     $rows[0]->es_gratuito,
                        estado:          $rows[0]->estado,
                        comprobante_url: $rows[0]->comprobante_url,
                        monto_pagado:    $rows[0]->monto_pagado,
                        nota_admin:      $rows[0]->nota_admin,
                        certificado_id:  $rows[0]->certificado_id,
                        created_at:      $rows[0]->created_at,
                        updated_at:      $rows[0]->updated_at,
                        nombre_cert:     $rows[0]->nombre_cert,
                        nombre_programa: $rows[0]->nombre_programa,
                        certificado_url: $certUrl,
                    );
                }

                $creadas = array_merge($creadas, $rows);
            }

            if (! empty($command->items_pago_ids)) {
                $itemsMap = collect($allItems)->keyBy('id');
                $pagoRows = [];

                foreach ($command->items_pago_ids as $itemId) {
                    $item = $itemsMap->get($itemId);
                    if (! $item || $item->es_gratuito || ! $item->activo) {
                        continue;
                    }

                    $estado          = $command->pagar_ahora ? 'pendiente_revision' : 'pendiente_pago';
                    $comprobanteUrl  = $command->pagar_ahora ? $command->comprobante_url : null;

                    $pagoRows[] = [
                        'config_item_id'  => $item->id,
                        'inscripcion_id'  => $command->inscripcion_id,
                        'usuario_ci'      => $command->usuario_ci,
                        'usuario_nombre'  => $command->usuario_nombre,
                        'usuario_email'   => $command->usuario_email,
                        'es_gratuito'     => false,
                        'estado'          => $estado,
                        'comprobante_url' => $comprobanteUrl,
                        'monto_pagado'    => $item->precio,
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ];
                }

                if (! empty($pagoRows)) {
                    $rows    = $this->solicitudRepo->createMany($pagoRows);
                    $creadas = array_merge($creadas, $rows);
                }
            }

            return $creadas;
        });
    }
}
