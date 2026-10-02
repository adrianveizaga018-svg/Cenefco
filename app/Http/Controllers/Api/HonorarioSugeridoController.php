<?php

namespace App\Http\Controllers\Api;

use App\Application\Honorarios\Queries\GetHonorariosSugeridosDelMesQuery;
use App\Application\Honorarios\QueryHandlers\GetHonorariosSugeridosDelMesQueryHandler;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Honorarios')]
class HonorarioSugeridoController extends Controller
{
    public function __construct(
        private readonly GetHonorariosSugeridosDelMesQueryHandler $handler,
    ) {}

    #[OA\Get(path: '/api/v1/honorarios-sugeridos', summary: 'Docentes activos del mes con monto sugerido', tags: ['Honorarios'])]
    public function index(Request $request): JsonResponse
    {
        $anio = (int) $request->get('anio', now()->year);
        $mes  = (int) $request->get('mes', now()->month);

        return response()->json($this->handler->handle(new GetHonorariosSugeridosDelMesQuery($anio, $mes)));
    }
}
