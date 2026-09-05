<?php

namespace App\Http\Controllers\Api;

use App\Application\Dashboard\Queries\GetDashboardGastosQuery;
use App\Application\Dashboard\QueryHandlers\GetDashboardGastosQueryHandler;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Dashboard')]
class DashboardGastosController extends Controller
{
    public function __construct(
        private readonly GetDashboardGastosQueryHandler $handler,
    ) {}

    #[OA\Get(path: '/api/v1/dashboard-gastos', summary: 'Resumen gerencial de ingresos y gastos del mes', tags: ['Dashboard'])]
    public function index(Request $request): JsonResponse
    {
        $anio = (int) $request->get('anio', now()->year);
        $mes  = (int) $request->get('mes', now()->month);

        return response()->json($this->handler->handle(new GetDashboardGastosQuery($anio, $mes)));
    }
}
