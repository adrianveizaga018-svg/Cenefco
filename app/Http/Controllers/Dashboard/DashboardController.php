<?php

namespace App\Http\Controllers\Dashboard;

use App\Application\Dashboard\Queries\GetDashboardStatsQuery;
use App\Application\Dashboard\QueryHandlers\GetDashboardStatsQueryHandler;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        private readonly GetDashboardStatsQueryHandler $statsHandler,
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->statsHandler->handle(new GetDashboardStatsQuery()));
    }
}
