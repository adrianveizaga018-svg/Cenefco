<?php

namespace App\Http\Controllers\Api;

use App\Application\Vendedores\Commands\CreateVendedorCommand;
use App\Application\Vendedores\Commands\DeleteVendedorCommand;
use App\Application\Vendedores\Commands\UpdateVendedorCommand;
use App\Application\Vendedores\Handlers\CreateVendedorHandler;
use App\Application\Vendedores\Handlers\DeleteVendedorHandler;
use App\Application\Vendedores\Handlers\UpdateVendedorHandler;
use App\Application\Vendedores\Queries\GetVendedorByIdQuery;
use App\Application\Vendedores\Queries\GetVendedoresQuery;
use App\Application\Vendedores\Queries\GetVendedorComisionDetalleQuery;
use App\Application\Vendedores\QueryHandlers\GetVendedorByIdQueryHandler;
use App\Application\Vendedores\QueryHandlers\GetVendedoresQueryHandler;
use App\Application\Vendedores\QueryHandlers\GetVendedorComisionDetalleQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Vendedores\StoreVendedorRequest;
use App\Http\Requests\Vendedores\UpdateVendedorRequest;
use App\Infrastructure\Vendedores\Services\VendedorScopeResolver;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VendedorController extends Controller
{
    public function __construct(
        private readonly GetVendedoresQueryHandler              $getVendedoresHandler,
        private readonly GetVendedorByIdQueryHandler             $getVendedorByIdHandler,
        private readonly GetVendedorComisionDetalleQueryHandler  $getComisionDetalleHandler,
        private readonly CreateVendedorHandler                   $createHandler,
        private readonly UpdateVendedorHandler                   $updateHandler,
        private readonly DeleteVendedorHandler                   $deleteHandler,
        private readonly VendedorScopeResolver                   $vendedorScope,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 20),
            'query'     => $request->get('query', ''),
            'sortKey'   => $request->input('sort.key', 'nombre'),
            'sortOrder' => $request->input('sort.order', 'asc'),
        ]);

        $activo = $request->has('activo') ? $request->boolean('activo') : null;

        return response()->json(
            $this->getVendedoresHandler->handle(new GetVendedoresQuery($pagination, $activo))
        );
    }

    public function store(StoreVendedorRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateVendedorCommand(
            nombre:      $request->nombre,
            apellido:    $request->apellido,
            ci:          $request->ci,
            telefono:    $request->telefono,
            email:       $request->email,
            foto:        $request->foto,
            pagina:      $request->pagina,
            meta_ventas: $request->filled('meta_ventas') ? (float) $request->meta_ventas : null,
            activo:      $request->boolean('activo', true),
            usuario_id:  $request->filled('usuario_id') ? (int) $request->usuario_id    : null,
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        $this->vendedorScope->assertAccesoVendedor(auth()->user(), $id);

        return response()->json(
            $this->getVendedorByIdHandler->handle(new GetVendedorByIdQuery($id))
        );
    }

    public function update(UpdateVendedorRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();

        return response()->json(
            $this->updateHandler->handle(new UpdateVendedorCommand(
                id:          $id,
                nombre:      $data['nombre']      ?? null,
                apellido:    $data['apellido']    ?? null,
                ci:          $data['ci']          ?? null,
                telefono:    $data['telefono']    ?? null,
                email:       $data['email']       ?? null,
                foto:        $data['foto']        ?? null,
                pagina:      $data['pagina']      ?? null,
                meta_ventas: isset($data['meta_ventas']) ? (float) $data['meta_ventas'] : null,
                activo:      array_key_exists('activo', $data) ? (bool) $data['activo'] : null,
                usuario_id:  isset($data['usuario_id'])  ? (int) $data['usuario_id']    : null,
                provided:    array_keys($data),
            ))
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteVendedorCommand($id));

        return response()->json(null, 204);
    }

    public function comisionDetalle(int $id): JsonResponse
    {
        $this->vendedorScope->assertAccesoVendedor(auth()->user(), $id);

        return response()->json(
            $this->getComisionDetalleHandler->handle(new GetVendedorComisionDetalleQuery($id))
        );
    }

    public function comisionDetallePdf(int $id): Response
    {
        $this->vendedorScope->assertAccesoVendedor(auth()->user(), $id);

        $dto = $this->getComisionDetalleHandler->handle(new GetVendedorComisionDetalleQuery($id));

        $pdf = Pdf::loadView('pdf.vendedor_comision_detalle', ['detalle' => $dto])
            ->setPaper('a4', 'portrait');

        return $pdf->download("comision-{$dto->vendedor_id}.pdf");
    }
}
