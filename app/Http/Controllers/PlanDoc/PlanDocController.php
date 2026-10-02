<?php

namespace App\Http\Controllers\PlanDoc;

use App\Application\PlanDoc\Queries\GetPlanDocByIdQuery;
use App\Application\PlanDoc\Queries\GetPlanDocsQuery;
use App\Application\PlanDoc\QueryHandlers\GetPlanDocByIdQueryHandler;
use App\Application\PlanDoc\QueryHandlers\GetPlanDocsQueryHandler;
use App\Http\Controllers\Controller;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanDocController extends Controller
{
    public function __construct(
        private readonly GetPlanDocsQueryHandler $listHandler,
        private readonly GetPlanDocByIdQueryHandler $showHandler,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $pagination = PaginationDTO::fromArray([
            'pageIndex' => $request->get('pageIndex', 1),
            'pageSize'  => $request->get('pageSize', 15),
        ]);

        return response()->json($this->listHandler->handle(new GetPlanDocsQuery(
            pagination: $pagination,
        )));
    }

    public function show(int $id): JsonResponse
    {
        return response()->json($this->showHandler->handle(new GetPlanDocByIdQuery($id)));
    }
}
