<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Settings\CobroEstadoSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CobroEstadoSettingsController extends Controller
{
    private const COLORES_VALIDOS = ['success', 'warning', 'danger', 'primary', 'info'];

    public function __construct(
        private readonly CobroEstadoSettings $settings
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->toArray());
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'completo_label' => 'required|string|max:30',
            'completo_color' => 'required|string|in:'.implode(',', self::COLORES_VALIDOS),
            'parcial_label' => 'required|string|max:30',
            'parcial_color' => 'required|string|in:'.implode(',', self::COLORES_VALIDOS),
            'sin_pagos_label' => 'required|string|max:30',
            'sin_pagos_color' => 'required|string|in:'.implode(',', self::COLORES_VALIDOS),
        ]);

        foreach ($validated as $key => $value) {
            $this->settings->$key = $value;
        }

        $this->settings->save();

        return response()->json([
            'message' => 'Configuración de estado de cobro actualizada correctamente',
            'settings' => $this->toArray(),
        ]);
    }

    private function toArray(): array
    {
        return [
            'completo_label' => $this->settings->completo_label,
            'completo_color' => $this->settings->completo_color,
            'parcial_label' => $this->settings->parcial_label,
            'parcial_color' => $this->settings->parcial_color,
            'sin_pagos_label' => $this->settings->sin_pagos_label,
            'sin_pagos_color' => $this->settings->sin_pagos_color,
            'colores_disponibles' => self::COLORES_VALIDOS,
        ];
    }
}
