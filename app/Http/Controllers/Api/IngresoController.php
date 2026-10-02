<?php

namespace App\Http\Controllers\Api;

use App\Application\Ingresos\Queries\GetIngresosQuery;
use App\Application\Ingresos\Queries\GetResumenIngresosQuery;
use App\Application\Ingresos\QueryHandlers\GetIngresosQueryHandler;
use App\Application\Ingresos\QueryHandlers\GetResumenIngresosQueryHandler;
use App\Http\Controllers\Controller;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IngresoController extends Controller
{
    public function __construct(
        private readonly GetIngresosQueryHandler       $getIngresosHandler,
        private readonly GetResumenIngresosQueryHandler $getResumenHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 20),
        ]);

        return response()->json(
            $this->getIngresosHandler->handle(new GetIngresosQuery(
                pagination: $pagination,
                desde:      $request->filled('desde') ? $request->get('desde') : null,
                hasta:      $request->filled('hasta') ? $request->get('hasta') : null,
            ))
        );
    }

    public function resumen(): JsonResponse
    {
        return response()->json(
            $this->getResumenHandler->handle(new GetResumenIngresosQuery())
        );
    }
}
