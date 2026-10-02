<?php

namespace App\Http\Controllers\Api;

use App\Application\ListaAprobados\Commands\CreateListaAprobadosCommand;
use App\Application\ListaAprobados\Commands\DeleteListaAprobadosBulkCommand;
use App\Application\ListaAprobados\Commands\DeleteListaAprobadosCommand;
use App\Application\ListaAprobados\Commands\UpdateListaAprobadosCommand;
use App\Application\ListaAprobados\Handlers\CreateListaAprobadosHandler;
use App\Application\ListaAprobados\Handlers\DeleteListaAprobadosBulkHandler;
use App\Application\ListaAprobados\Handlers\DeleteListaAprobadosHandler;
use App\Application\ListaAprobados\Handlers\UpdateListaAprobadosHandler;
use App\Application\ListaAprobados\Queries\GetListaAprobadoByIdQuery;
use App\Application\ListaAprobados\Queries\GetListaAprobadosQuery;
use App\Application\ListaAprobados\QueryHandlers\GetListaAprobadoByIdQueryHandler;
use App\Application\ListaAprobados\QueryHandlers\GetListaAprobadosQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListaAprobados\StoreListaAprobadosRequest;
use App\Http\Requests\ListaAprobados\UpdateListaAprobadosRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListaAprobadosController extends Controller
{
    public function __construct(
        private readonly GetListaAprobadosQueryHandler    $listHandler,
        private readonly GetListaAprobadoByIdQueryHandler $showHandler,
        private readonly CreateListaAprobadosHandler      $createHandler,
        private readonly UpdateListaAprobadosHandler      $updateHandler,
        private readonly DeleteListaAprobadosHandler      $deleteHandler,
        private readonly DeleteListaAprobadosBulkHandler  $deleteBulkHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 30),
            'query'     => $request->get('query', ''),
        ]);

        return response()->json(
            $this->listHandler->handle(new GetListaAprobadosQuery(
                pagination:        $pagination,
                imparteId:         $request->filled('imparte_id')         ? (int) $request->get('imparte_id')         : null,
                usuarioId:         $request->filled('usuario_id')         ? (int) $request->get('usuario_id')         : null,
                condicion:         $request->filled('condicion')          ? $request->get('condicion')                : null,
                estadoCertificado: $request->filled('estado_certificado') ? $request->get('estado_certificado')       : null,
            ))
        );
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->showHandler->handle(new GetListaAprobadoByIdQuery($id))
        );
    }

    public function store(StoreListaAprobadosRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateListaAprobadosCommand(
            imparte_id:         $request->integer('imparte_id'),
            usuario_id:         $request->integer('usuario_id'),
            inscripcion_id:     $request->filled('inscripcion_id')  ? $request->integer('inscripcion_id')    : null,
            nota_final:         $request->filled('nota_final')      ? (float) $request->input('nota_final')  : null,
            nota_minima:        $request->filled('nota_minima')     ? (float) $request->input('nota_minima') : null,
            condicion:          $request->input('condicion', 'aprobado'),
            observacion:        $request->input('observacion'),
            comprobante_url:    $request->input('comprobante_url'),
            ajuste_manual:      $request->boolean('ajuste_manual', false),
            estado_certificado: $request->input('estado_certificado', 'pendiente'),
            registrado_por:     $request->filled('registrado_por')  ? $request->integer('registrado_por')    : null,
            id_us_reg:          $request->integer('id_us_reg', 0),
        ));

        return response()->json($dto, 201);
    }

    public function update(UpdateListaAprobadosRequest $request, int $id): JsonResponse
    {
        
        
        $changes = [];
        if ($request->filled('nota_final'))         $changes['nota_final']         = (float) $request->input('nota_final');
        if ($request->filled('nota_minima'))         $changes['nota_minima']        = (float) $request->input('nota_minima');
        if ($request->filled('condicion'))           $changes['condicion']          = $request->input('condicion');
        if ($request->has('observacion'))            $changes['observacion']        = $request->input('observacion');
        if ($request->has('ajuste_manual'))          $changes['ajuste_manual']      = $request->boolean('ajuste_manual');
        if ($request->filled('estado_certificado'))  $changes['estado_certificado'] = $request->input('estado_certificado');
        if ($request->has('notificado_email'))       $changes['notificado_email']   = $request->boolean('notificado_email');
        if ($request->has('comprobante_url'))        $changes['comprobante_url']    = $request->input('comprobante_url');

        $dto = $this->updateHandler->handle(new UpdateListaAprobadosCommand(
            id:      $id,
            changes: $changes,
        ));

        return response()->json($dto);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteListaAprobadosCommand($id));

        return response()->json(null, 204);
    }

    public function destroyBulk(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'min:1'],
        ]);

        $eliminados = $this->deleteBulkHandler->handle(new DeleteListaAprobadosBulkCommand($data['ids']));

        return response()->json(['eliminados' => $eliminados]);
    }
}
