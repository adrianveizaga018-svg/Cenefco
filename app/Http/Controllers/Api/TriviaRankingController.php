<?php

namespace App\Http\Controllers\Api;

use App\Application\Trivia\Queries\GetTriviaRankingQuery;
use App\Application\Trivia\QueryHandlers\GetTriviaRankingQueryHandler;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TriviaRankingController extends Controller
{
    public function __construct(
        private readonly GetTriviaRankingQueryHandler $handler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $limite = min(50, max(1, (int) $request->query('limite', 20)));

        return response()->json([
            'data' => $this->handler->handle(new GetTriviaRankingQuery($limite)),
        ]);
    }
}
