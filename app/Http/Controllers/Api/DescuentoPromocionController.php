<?php

namespace App\Http\Controllers\Api;

use App\Application\DescuentosPromociones\Commands\CreateDescuentoPromocionCommand;
use App\Application\DescuentosPromociones\Commands\DeleteDescuentoPromocionCommand;
use App\Application\DescuentosPromociones\Commands\UpdateDescuentoPromocionCommand;
use App\Application\DescuentosPromociones\Handlers\CreateDescuentoPromocionHandler;
use App\Application\DescuentosPromociones\Handlers\DeleteDescuentoPromocionHandler;
use App\Application\DescuentosPromociones\Handlers\UpdateDescuentoPromocionHandler;
use App\Application\DescuentosPromociones\Queries\GetDescuentoPromocionByIdQuery;
use App\Application\DescuentosPromociones\Queries\GetDescuentosPromocionesQuery;
use App\Application\DescuentosPromociones\QueryHandlers\GetDescuentoPromocionByIdQueryHandler;
use App\Application\DescuentosPromociones\QueryHandlers\GetDescuentosPromocionesQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\DescuentosPromociones\StoreDescuentoPromocionRequest;
use App\Http\Requests\DescuentosPromociones\UpdateDescuentoPromocionRequest;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DescuentoPromocionController extends Controller
{
    public function __construct(
        private readonly GetDescuentosPromocionesQueryHandler $getDescuentosHandler,
        private readonly GetDescuentoPromocionByIdQueryHandler $getByIdHandler,
        private readonly CreateDescuentoPromocionHandler $createHandler,
        private readonly UpdateDescuentoPromocionHandler $updateHandler,
        private readonly DeleteDescuentoPromocionHandler $deleteHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 20),
            'query'     => $request->get('query', ''),
            'sortKey'   => $request->input('sort.key', 'id'),
            'sortOrder' => $request->input('sort.order', 'desc'),
        ]);

        return response()->json(
            $this->getDescuentosHandler->handle(
                new GetDescuentosPromocionesQuery($pagination, $request->boolean('soloActivos', false))
            )
        );
    }

    public function store(StoreDescuentoPromocionRequest $request): JsonResponse
    {
        $dto = $this->createHandler->handle(new CreateDescuentoPromocionCommand(
            codigo:           $request->codigo,
            nombre:           $request->nombre,
            valor:            (float) $request->valor,
            tipo_descuento:   $request->input('tipo_descuento', 'porcentaje'),
            descripcion:      $request->descripcion,
            monto_minimo:     $request->monto_minimo ? (float) $request->monto_minimo : null,
            usos_maximos:     $request->usos_maximos ? (int) $request->usos_maximos : null,
            usos_por_usuario: $request->integer('usos_por_usuario', 1),
            programa_id:      $request->programa_id ? (int) $request->programa_id : null,
            activo:           $request->boolean('activo', true),
            fecha_inicio:     $request->fecha_inicio,
            fecha_fin:        $request->fecha_fin,
        ));

        return response()->json($dto, 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->getByIdHandler->handle(new GetDescuentoPromocionByIdQuery($id)));
    }

    public function update(UpdateDescuentoPromocionRequest $request, int $id): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateDescuentoPromocionCommand(
            id:               $id,
            nombre:           $request->nombre,
            valor:            $request->has('valor') ? (float) $request->valor : null,
            tipo_descuento:   $request->tipo_descuento,
            descripcion:      $request->descripcion,
            monto_minimo:     $request->has('monto_minimo') ? (float) $request->monto_minimo : null,
            usos_maximos:     $request->has('usos_maximos') ? (int) $request->usos_maximos : null,
            usos_por_usuario: $request->has('usos_por_usuario') ? $request->integer('usos_por_usuario') : null,
            programa_id:      $request->has('programa_id') ? (int) $request->programa_id : null,
            activo:           $request->has('activo') ? $request->boolean('activo') : null,
            fecha_inicio:     $request->fecha_inicio,
            fecha_fin:        $request->fecha_fin,
        )));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->deleteHandler->handle(new DeleteDescuentoPromocionCommand($id));

        return response()->json(null, 204);
    }
}
