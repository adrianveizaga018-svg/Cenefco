<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfiguracionSitioController extends Controller
{
    public function publica(): JsonResponse
    {
        $rows = DB::table('web_configuracion_sitio')
            ->where('es_publica', true)
            ->get(['clave', 'valor']);

        $data = $rows->pluck('valor', 'clave');

        return response()->json(['data' => $data]);
    }

    public function index(): JsonResponse
    {
        $rows = DB::table('web_configuracion_sitio')
            ->orderBy('grupo')
            ->orderBy('clave')
            ->get();

        return response()->json(['data' => $rows]);
    }

    public function update(Request $request): JsonResponse
    {
        $items = $request->validate([
            'items'         => ['required', 'array'],
            'items.*.clave' => ['required', 'string'],
            'items.*.valor' => ['nullable', 'string'],
        ])['items'];

        foreach ($items as $item) {
            DB::table('web_configuracion_sitio')
                ->where('clave', $item['clave'])
                ->update([
                    'valor'      => $item['valor'] ?? '',
                    'updated_at' => now(),
                ]);
        }

        return response()->json(['message' => 'Configuración actualizada.']);
    }
}
