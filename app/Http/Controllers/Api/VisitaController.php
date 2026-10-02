<?php

namespace App\Http\Controllers\Api;

use App\Application\Visitas\Commands\CreateVisitaCommand;
use App\Application\Visitas\Handlers\CreateVisitaHandler;
use App\Application\Visitas\Queries\GetVisitasStatsQuery;
use App\Application\Visitas\QueryHandlers\GetVisitasStatsQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Visitas\StoreVisitaRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VisitaController extends Controller
{
    public function __construct(
        private readonly CreateVisitaHandler          $createHandler,
        private readonly GetVisitasStatsQueryHandler  $statsHandler,
    ) {}

    public function store(StoreVisitaRequest $request): JsonResponse
    {
        $ua          = $request->userAgent() ?? '';
        $dispositivo = $request->input('dispositivo') ?? $this->parseDispositivo($ua);
        $navegador   = $request->input('navegador')   ?? $this->parseNavegador($ua);
        $so          = $request->input('so')           ?? $this->parseSO($ua);

        $dto = $this->createHandler->handle(new CreateVisitaCommand(
            session_id:   $request->session_id,
            url:          $request->url,
            ruta:         $request->ruta,
            titulo:       $request->titulo,
            referrer:     $request->referrer,
            pais:         $request->pais,
            ciudad:       $request->ciudad,
            dispositivo:  $dispositivo,
            navegador:    $navegador,
            so:           $so,
            duracion_seg: $request->duracion_seg ? (int) $request->duracion_seg : null,
        ));

        return response()->json($dto, 201);
    }

    public function stats(Request $request): JsonResponse
    {
        $periodo = (int) $request->get('periodo', 30);
        $hasta   = now()->toDateString();
        $desde   = now()->subDays($periodo)->toDateString();

        return response()->json(
            $this->statsHandler->handle(new GetVisitasStatsQuery($desde, $hasta))
        );
    }

    private function parseDispositivo(string $ua): string
    {
        if (preg_match('/Mobile|Android.*Mobile|iPhone|Windows Phone/i', $ua)) return 'mobile';
        if (preg_match('/iPad|Android(?!.*Mobile)|Tablet/i', $ua)) return 'tablet';
        return 'desktop';
    }

    private function parseNavegador(string $ua): string
    {
        if (str_contains($ua, 'Edg/') || str_contains($ua, 'Edge/')) return 'Edge';
        if (str_contains($ua, 'OPR/') || str_contains($ua, 'Opera/')) return 'Opera';
        if (str_contains($ua, 'Chrome/')) return 'Chrome';
        if (str_contains($ua, 'Firefox/')) return 'Firefox';
        if (str_contains($ua, 'Safari/') && ! str_contains($ua, 'Chrome/')) return 'Safari';
        return 'Otro';
    }

    private function parseSO(string $ua): string
    {
        if (str_contains($ua, 'Windows')) return 'Windows';
        if (str_contains($ua, 'Mac OS X')) return 'macOS';
        if (str_contains($ua, 'Android')) return 'Android';
        if (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) return 'iOS';
        if (str_contains($ua, 'Linux')) return 'Linux';
        return 'Otro';
    }
}
