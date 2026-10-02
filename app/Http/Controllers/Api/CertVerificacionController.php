<?php

namespace App\Http\Controllers\Api;

use App\Application\CertVerificaciones\Commands\CreateCertVerificacionCommand;
use App\Application\CertVerificaciones\Handlers\CreateCertVerificacionHandler;
use App\Application\CertVerificaciones\Queries\GetCertVerificacionByIdQuery;
use App\Application\CertVerificaciones\Queries\GetCertVerificacionesQuery;
use App\Application\CertVerificaciones\QueryHandlers\GetCertVerificacionByIdQueryHandler;
use App\Application\CertVerificaciones\QueryHandlers\GetCertVerificacionesQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\CertVerificaciones\StoreCertVerificacionRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertVerificacionController extends Controller
{
    public function __construct(
        private readonly GetCertVerificacionesQueryHandler   $listHandler,
        private readonly GetCertVerificacionByIdQueryHandler $showHandler,
        private readonly CreateCertVerificacionHandler       $createHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 30),
        ]);

        return response()->json(
            $this->listHandler->handle(new GetCertVerificacionesQuery(
                pagination:       $pagination,
                certificadoId:    $request->filled('certificado_id')    ? (int) $request->get('certificado_id') : null,
                codigoConsultado: $request->filled('codigo_consultado') ? $request->get('codigo_consultado')    : null,
                resultado:        $request->filled('resultado')         ? $request->get('resultado')            : null,
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->showHandler->handle(new GetCertVerificacionByIdQuery($id))
        );
    }

    public function store(StoreCertVerificacionRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateCertVerificacionCommand(
            certificado_id:    $request->filled('certificado_id') ? $request->integer('certificado_id') : null,
            codigo_consultado: $request->input('codigo_consultado'),
            resultado:         $request->input('resultado'),
            ip_origen:         $request->input('ip_origen', $request->ip()),
            user_agent:        $request->input('user_agent', $request->userAgent()),
            pais:              $request->input('pais'),
        ));

        return response()->json($dto, 201);
    }
}
