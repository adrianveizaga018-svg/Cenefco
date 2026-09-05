<?php

namespace App\Http\Controllers\Api;

use App\Application\CertConfigProgramas\Commands\CrearSolicitudesCommand;
use App\Application\CertConfigProgramas\Commands\SubirComprobanteCommand;
use App\Application\CertConfigProgramas\DTOs\CertConfigProgramaDTO;
use App\Application\CertConfigProgramas\Handlers\CrearSolicitudesHandler;
use App\Application\CertConfigProgramas\Handlers\SubirComprobanteHandler;
use App\Application\CertConfigProgramas\Queries\GetCertConfigByProgramaQuery;
use App\Application\CertConfigProgramas\QueryHandlers\GetCertConfigByProgramaQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\CertConfigProgramas\CrearSolicitudesPortalRequest;
use App\Http\Requests\CertConfigProgramas\SubirComprobanteRequest;
use Illuminate\Http\JsonResponse;

class CertConfigPublicController extends Controller
{
    public function __construct(
        private readonly GetCertConfigByProgramaQueryHandler $getConfigHandler,
        private readonly CrearSolicitudesHandler $crearSolicitudesHandler,
        private readonly SubirComprobanteHandler $subirComprobanteHandler,
    ) {}

    public function getConfig(int $programaId): JsonResponse
    {
        $config = $this->getConfigHandler->handle(new GetCertConfigByProgramaQuery($programaId));

        if (! $config || ! $config->activo) {
            return response()->json(['activo' => false]);
        }

        $itemsActivos = array_values(array_filter($config->items, fn ($i) => $i->activo));
        $config = new CertConfigProgramaDTO(
            id:              $config->id,
            programa_id:     $config->programa_id,
            activo:          $config->activo,
            titulo:          $config->titulo,
            descripcion:     $config->descripcion,
            created_at:      $config->created_at,
            updated_at:      $config->updated_at,
            items:           $itemsActivos,
            nombre_programa: $config->nombre_programa,
        );

        return response()->json(['activo' => true, 'config' => $config]);
    }

    public function crearSolicitudes(CrearSolicitudesPortalRequest $request): JsonResponse
    {
        $solicitudes = $this->crearSolicitudesHandler->handle(new CrearSolicitudesCommand(
            programa_id:     $request->integer('programa_id'),
            usuario_ci:      $request->input('usuario_ci'),
            usuario_nombre:  $request->input('usuario_nombre'),
            usuario_email:   $request->input('usuario_email'),
            inscripcion_id:  $request->integer('inscripcion_id') ?: null,
            items_pago_ids:  $request->input('items_pago_ids', []),
            pagar_ahora:     $request->boolean('pagar_ahora', false),
            comprobante_url: $request->input('comprobante_url'),
        ));

        return response()->json(['solicitudes' => $solicitudes], 201);
    }

    public function subirComprobante(SubirComprobanteRequest $request, int $id): JsonResponse
    {
        $dto = $this->subirComprobanteHandler->handle(new SubirComprobanteCommand(
            solicitud_id:    $id,
            comprobante_url: $request->input('comprobante_url'),
        ));

        return response()->json($dto);
    }
}
