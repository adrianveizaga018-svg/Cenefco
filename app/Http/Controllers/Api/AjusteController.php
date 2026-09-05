<?php

namespace App\Http\Controllers\Api;

use App\Application\Ajustes\Commands\UpdateAjusteCommand;
use App\Application\Ajustes\Handlers\UpdateAjusteHandler;
use App\Application\Ajustes\Queries\GetAjusteByKeyQuery;
use App\Application\Ajustes\Queries\GetAllAjustesQuery;
use App\Application\Ajustes\QueryHandlers\GetAjusteByKeyQueryHandler;
use App\Application\Ajustes\QueryHandlers\GetAllAjustesQueryHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ajustes\UpdateAjusteRequest;
use Illuminate\Http\JsonResponse;

class AjusteController extends Controller
{
    public function __construct(
        private readonly GetAllAjustesQueryHandler $getAllHandler,
        private readonly GetAjusteByKeyQueryHandler $getByKeyHandler,
        private readonly UpdateAjusteHandler $updateHandler,
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->getAllHandler->handle(new GetAllAjustesQuery));
    }

    public function show(string $key): JsonResponse
    {
        return response()->json($this->getByKeyHandler->handle(new GetAjusteByKeyQuery($key)));
    }

    public function update(UpdateAjusteRequest $request, string $key): JsonResponse
    {
        return response()->json($this->updateHandler->handle(new UpdateAjusteCommand($key, $request->valor)));
    }
}
