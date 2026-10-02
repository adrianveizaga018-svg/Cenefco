<?php

namespace App\Http\Controllers\Api;

use App\Domain\ConfigSitio\Exceptions\ConfigSitioNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConfigSitio\UpdateConfigSitioRequest;
use App\Infrastructure\ConfigSitio\Models\ConfigSitio;
use Illuminate\Http\JsonResponse;

class ConfigSitioController extends Controller
{
    public function show(): JsonResponse
    {
        $config = ConfigSitio::first();
        if (! $config) {
            throw new ConfigSitioNotFoundException;
        }

        return response()->json($config);
    }

    public function update(UpdateConfigSitioRequest $request): JsonResponse
    {
        $config = ConfigSitio::first();
        if (! $config) {
            throw new ConfigSitioNotFoundException;
        }

        $config->update($request->validated());

        return response()->json($config);
    }
}
